<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\TourSchedule;
use App\Models\UserIdentity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TourBookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:tour_schedules,id',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email|max:255',
            'total_price' => 'required|numeric',
            'transport_type' => 'required|in:flight,self',
            'identity_number' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date',
            'issue_place' => 'required|string|max:255',
            'front_image' => 'nullable|image|max:5120',
            'back_image' => 'nullable|image|max:5120',
        ]);

        $user = Auth::user();
        if ($request->filled('customer_phone')) {
            $user->phone = $request->customer_phone;
            $user->save();
        }
        $identity = UserIdentity::where('user_id', $user->id)->first();

        if (! $identity) {
            $identity = new UserIdentity;
            $identity->user_id = $user->id;
        } else {
            // If updating, make sure we're not violating unique constraint
            // by checking if identity_number already exists for another user
            $existingIdentity = UserIdentity::where('identity_number', $request->identity_number)
                ->where('user_id', '!=', $user->id)
                ->first();

            if ($existingIdentity) {
                return redirect()->back()
                    ->with('error', 'Số CCCD/Hộ chiếu này đã được đăng ký bởi người dùng khác. Vui lòng kiểm tra lại.');
            }
        }

        $identity->full_name = $request->customer_name;
        $identity->identity_number = $request->identity_number;
        $identity->date_of_birth = $request->date_of_birth;
        $identity->gender = $request->gender;
        $identity->issue_date = $request->issue_date;
        $identity->expiry_date = $request->expiry_date;
        $identity->issue_place = $request->issue_place;

        if ($request->hasFile('front_image')) {
            $frontPath = $request->file('front_image')->store('identities', 'public');
            $identity->front_image_url = '/storage/' . $frontPath;
        }

        if ($request->hasFile('back_image')) {
            $backPath = $request->file('back_image')->store('identities', 'public');
            $identity->back_image_url = '/storage/' . $backPath;
        }

        $identity->save();

        $booking = new Booking;
        $booking->user_id = $user->id;
        $booking->tour_schedule_id = $request->schedule_id;
        $booking->adults_count = $request->adults;
        $booking->children_count = $request->children;
        $booking->total_price = $request->total_price;
        $booking->booking_status = 'pending';
        $booking->save();

        $passenger = new BookingPassenger;
        $passenger->booking_id = $booking->id;
        $passenger->full_name = $request->customer_name;
        $passenger->date_of_birth = $request->date_of_birth;
        $passenger->identity_number = $request->identity_number;
        $passenger->gender = $request->gender;
        $passenger->passenger_type = 'adult';
        $passenger->save();

        if ($request->transport_type === 'flight') {
            $schedule = TourSchedule::with('tour.destination', 'tour.departure_location')->find($request->schedule_id);
            $departureDate = Carbon::parse($schedule->departure_date)->format('Y-m-d');

            $iataMap = [
                'Đà Nẵng' => 'DAD',
                'Thành Phố Hồ Chí Minh' => 'SGN',
                'Hà Nội' => 'HAN',
                'Phú Quốc' => 'PQC',
                'Nha Trang' => 'CXR',
                'Huế' => 'HUI',
                'Vinh' => 'VII',
                'Đà Lạt' => 'DLI',
                'Hải Phòng' => 'HPH',
            ];

            $originCode = $iataMap[$schedule->tour->departure_location->name ?? ''] ?? 'HAN';
            $destinationCode = $iataMap[$schedule->tour->destination->name ?? ''] ?? 'SGN';
            $totalPassengers = $request->adults + $request->children;

            return redirect()->route('frontend.flights.search', [
                'origin' => $originCode,
                'destination' => $destinationCode,
                'departure_date' => $departureDate,
                'passengers' => $totalPassengers,
                'cabin_class' => 'economy',
                'tour_booking_id' => $booking->id
            ])->with('success', 'đặt tour thành công. hệ thống đang tìm chuyến bay phù hợp.');
        }

        return redirect()->route('home')->with('success', 'đặt tour thành công. chúng tôi sẽ liên hệ sớm để xác nhận lịch trình tự túc.');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:tour_schedules,id',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
        ]);

        $schedule = TourSchedule::with('tour')->findOrFail($request->schedule_id);
        $totalPersons = $request->adults + $request->children;
        $totalPrice = $schedule->tour->base_price * $totalPersons;

        // Lấy thông tin định danh của user hiện tại (nếu có)
        $user = Auth::user();
        $user->load('identity');
        $identity = $user->identity;

        return view('frontend.tours.checkout', [
            'schedule' => $schedule,
            'adults' => $request->adults,
            'children' => $request->children,
            'totalPersons' => $totalPersons,
            'totalPrice' => $totalPrice,
            'user' => $user,
            'identity' => $identity, // Có thể null nếu user chưa cập nhật CCCD/Hộ chiếu
        ]);
    }
}
