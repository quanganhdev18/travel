<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\TourBookingMail;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Payment;
use App\Models\TourSchedule;
use App\Models\UserIdentity;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
            'transport_type' => 'required|in:flight,bus,self',
            'identity_number' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date',
            'issue_place' => 'required|string|max:255',
            'front_image' => 'nullable|image|max:5120',
            'back_image' => 'nullable|image|max:5120',
            'payment_method' => 'required|in:cod,vnpay',
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
            $identity->front_image_url = '/storage/'.$frontPath;
        }

        if ($request->hasFile('back_image')) {
            $backPath = $request->file('back_image')->store('identities', 'public');
            $identity->back_image_url = '/storage/'.$backPath;
        }

        $identity->save();

        $booking = new Booking;
        $booking->user_id = $user->id;
        $booking->tour_schedule_id = $request->schedule_id;
        $booking->adults_count = $request->adults;
        $booking->children_count = $request->children;
        $booking->total_price = $request->total_price;
        $booking->booking_status = 'pending';
        $booking->transport_type = $request->transport_type;
        $booking->save();

        $passenger = new BookingPassenger;
        $passenger->booking_id = $booking->id;
        $passenger->full_name = $request->customer_name;
        $passenger->date_of_birth = $request->date_of_birth;
        $passenger->identity_number = $request->identity_number;
        $passenger->gender = $request->gender;
        $passenger->passenger_type = 'adult';
        $passenger->save();

        $schedule = TourSchedule::with('tour')->find($request->schedule_id);

        try {
            Mail::to($request->customer_email)->send(
                new TourBookingMail($booking, $schedule, $request->customer_name, $request->customer_phone)
            );
        } catch (\Exception $e) {
            Log::error('Lỗi gửi mail đặt tour: '.$e->getMessage());
        }

        if ($request->payment_method === 'vnpay') {
            $vnpayUrl = $this->generateVnpayUrl($booking, $request->ip());

            return redirect()->away($vnpayUrl);
        }

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
                'tour_booking_id' => $booking->id,
            ])->with('success', 'đặt tour thành công. hệ thống đang tìm chuyến bay phù hợp.');
        }

        if ($request->transport_type === 'bus') {
            return redirect()->route('home')->with('success', 'Đặt tour thành công. Chúng tôi sẽ liên hệ sớm để xác nhận lịch trình di chuyển bằng xe.');
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

    public function payWithVNPay(int $id, Request $request): RedirectResponse
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->booking_status === 'cancelled' || $booking->booking_status === 'completed') {
            return redirect()->route('user.bookings')->with('error', 'Đơn hàng không thể thanh toán.');
        }

        $vnpayUrl = $this->generateVnpayUrl($booking, $request->ip());

        return redirect()->away($vnpayUrl);
    }

    private function generateVnpayUrl(Booking $booking, string $ipAddress): string
    {
        $vnp_TmnCode = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_Url = config('vnpay.url');
        $vnp_Returnurl = route('frontend.tours.vnpay_return');

        $vnp_TxnRef = $booking->id.'_'.time();
        $vnp_OrderInfo = 'Thanh toan dat tour #'.str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT);
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = (int) ($booking->total_price * 100);
        $vnp_Locale = 'vi';
        $vnp_IpAddr = $ipAddress;

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_ReturnUrl' => $vnp_Returnurl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'payment_method' => 'vnpay',
            'transaction_code' => $vnp_TxnRef,
            'payment_status' => 'pending',
        ]);

        ksort($inputData);
        $query = '';
        $i = 0;
        $hashdata = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&'.urlencode($key).'='.urlencode($value);
                $query .= '&'.urlencode($key).'='.urlencode($value);
            } else {
                $hashdata .= urlencode($key).'='.urlencode($value);
                $query .= urlencode($key).'='.urlencode($value);
                $i = 1;
            }
        }

        $vnp_Url = $vnp_Url.'?'.$query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= '&vnp_SecureHash='.$vnpSecureHash;
        }

        Log::debug('VNPay Redirect URL: '.$vnp_Url);

        return $vnp_Url;
    }

    public function vnpayReturn(Request $request): RedirectResponse
    {
        $vnp_SecureHash = $request->vnp_SecureHash;
        $vnp_HashSecret = config('vnpay.hash_secret');

        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == 'vnp_') {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&'.urlencode($key).'='.urlencode($value);
            } else {
                $hashData .= urlencode($key).'='.urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            $txnRef = $request->vnp_TxnRef;
            $parts = explode('_', $txnRef);
            $bookingId = $parts[0] ?? null;

            $booking = Booking::with('tour_schedule.tour')->find($bookingId);
            $payment = Payment::where('transaction_code', $txnRef)->first();

            if ($request->vnp_ResponseCode == '00') {
                if ($payment) {
                    $payment->update([
                        'payment_status' => 'success',
                        'paid_at' => now(),
                    ]);
                }

                if ($booking) {
                    $booking->update([
                        'booking_status' => 'confirmed',
                    ]);
                }

                if ($booking && $booking->transport_type === 'flight') {
                    $schedule = TourSchedule::with('tour.destination', 'tour.departure_location')->find($booking->tour_schedule_id);
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
                    $totalPassengers = $booking->adults_count + $booking->children_count;

                    return redirect()->route('frontend.flights.search', [
                        'origin' => $originCode,
                        'destination' => $destinationCode,
                        'departure_date' => $departureDate,
                        'passengers' => $totalPassengers,
                        'cabin_class' => 'economy',
                        'tour_booking_id' => $booking->id,
                    ])->with('success', 'Thanh toán VNPay thành công. Hệ thống đang tìm chuyến bay phù hợp.');
                }

                return redirect()->route('user.bookings')->with('success', 'Thanh toán đặt tour qua VNPay thành công!');
            } else {
                if ($payment) {
                    $payment->update([
                        'payment_status' => 'failed',
                    ]);
                }

                return redirect()->route('user.bookings')->with('error', 'Thanh toán không thành công. Mã lỗi: '.$request->vnp_ResponseCode);
            }
        }

        return redirect()->route('user.bookings')->with('error', 'Chữ ký thanh toán không hợp lệ.');
    }

    public function vnpayIpn(Request $request): JsonResponse
    {
        $vnp_SecureHash = $request->vnp_SecureHash;
        $vnp_HashSecret = config('vnpay.hash_secret');

        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == 'vnp_') {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&'.urlencode($key).'='.urlencode($value);
            } else {
                $hashData .= urlencode($key).'='.urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            try {
                $txnRef = $request->vnp_TxnRef;
                $parts = explode('_', $txnRef);
                $bookingId = $parts[0] ?? null;

                $booking = Booking::find($bookingId);
                $payment = Payment::where('transaction_code', $txnRef)->first();

                if (! $booking || ! $payment) {
                    return response()->json([
                        'RspCode' => '01',
                        'Message' => 'Order not found',
                    ]);
                }

                $vnpAmount = $request->vnp_Amount / 100;
                if ($vnpAmount != $payment->amount) {
                    return response()->json([
                        'RspCode' => '04',
                        'Message' => 'Invalid amount',
                    ]);
                }

                if ($payment->payment_status !== 'pending') {
                    return response()->json([
                        'RspCode' => '02',
                        'Message' => 'Order already confirmed',
                    ]);
                }

                if ($request->vnp_ResponseCode == '00') {
                    $payment->update([
                        'payment_status' => 'success',
                        'paid_at' => now(),
                    ]);
                    $booking->update([
                        'booking_status' => 'confirmed',
                    ]);
                } else {
                    $payment->update([
                        'payment_status' => 'failed',
                    ]);
                }

                return response()->json([
                    'RspCode' => '00',
                    'Message' => 'Confirm success',
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'RspCode' => '99',
                    'Message' => 'Unknown error',
                ]);
            }
        }

        return response()->json([
            'RspCode' => '97',
            'Message' => 'Invalid signature',
        ]);
    }
}
