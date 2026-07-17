<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreTourBookingRequest;
use App\Mail\TourBookingMail;
use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Holiday;
use App\Models\Payment;
use App\Models\TourSchedule;
use App\Services\FlightBookingService;
use App\Services\TourBookingService;
use App\Services\VnPayService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TourBookingController extends Controller
{
    public function __construct(
        protected TourBookingService $bookingService,
        protected FlightBookingService $flightService,
        protected VnPayService $vnPayService
    ) {}

    public function store(StoreTourBookingRequest $request)
    {
        $user = Auth::user();
        $sessionId = session()->getId();

        try {
            $booking = $this->bookingService->createBooking($request->validated(), $user, $sessionId);
            $schedule = $booking->tour_schedule;
        } catch (Exception $e) {
            Log::error('Lỗi đặt tour: '.$e->getMessage());

            return redirect()->back()->with('error', $e->getMessage() ?: 'Đã có lỗi xảy ra trong quá trình đặt tour. Vui lòng thử lại.');
        }

        try {
            Mail::to($request->customer_email)->send(
                new TourBookingMail($booking, $schedule, $request->customer_name, $request->customer_phone)
            );
        } catch (Exception $e) {
            Log::error('Lỗi gửi mail đặt tour: '.$e->getMessage());
        }

        if ($request->payment_method === 'vnpay') {
            $vnpayUrl = $this->vnPayService->generateUrl($booking, $request->ip());

            return redirect()->away($vnpayUrl);
        }

        // Nếu thanh toán tiền mặt (COD), tiến hành xuất vé máy bay nếu chọn máy bay
        if ($booking->transport_type === 'flight') {
            $this->flightService->bookFlightForBooking($booking);
        }

        if ($request->transport_type === 'flight') {
            return redirect()->route('frontend.tours.booking_success', $booking->id)->with('success', 'Đặt tour và vé máy bay thành công. Vui lòng thanh toán sớm để giữ chỗ.');
        }

        if ($request->transport_type === 'bus') {
            return redirect()->route('frontend.tours.booking_success', $booking->id)->with('success', 'Đặt tour thành công. Chúng tôi sẽ liên hệ sớm để xác nhận chuyến xe.');
        }

        return redirect()->route('frontend.tours.booking_success', $booking->id)->with('success', 'Đặt tour thành công. Bạn tự túc phương tiện di chuyển.');
    }

    public function bookingSuccess($id)
    {
        $booking = Booking::with(['tour_schedule.tour'])->findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        return view('frontend.tours.booking_success', compact('booking'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:tour_schedules,id',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
        ]);

        $schedule = TourSchedule::with(['tour.tickets.ticket_options', 'tour.addons'])->findOrFail($request->schedule_id);

        if ($schedule->status !== 'available' || Carbon::parse($schedule->departure_date)->lt(Carbon::today()->addDays(3))) {
            return redirect()->back()->with('error', 'Tour khởi hành trong vòng 3 ngày tới không thể đặt trực tuyến. Vui lòng chọn lịch trình khác.');
        }
        $totalPersons = $request->adults + $request->children;

        // Cơ chế giữ chỗ (Seat Hold) qua Cache (15 phút)
        $holdKey = "tour_schedule_{$schedule->id}_holds";
        $currentHolds = Cache::get($holdKey, []);

        // Dọn dẹp holds hết hạn
        $currentHolds = array_filter($currentHolds, function ($h) {
            return $h['expires_at'] > now()->timestamp;
        });

        // Tính tổng chỗ đang bị giữ bởi những người khác
        $userId = Auth::id() ?? session()->getId();
        $otherHolds = array_filter($currentHolds, function ($h, $k) use ($userId) {
            return $k !== $userId;
        }, ARRAY_FILTER_USE_BOTH);

        $totalHeldByOthers = array_sum(array_column($otherHolds, 'seats'));

        if ($schedule->available_seats - $totalHeldByOthers < $totalPersons) {
            return redirect()->back()->with('error', 'Tour đang có người khác giữ chỗ đang thanh toán. Vui lòng thử lại sau ít phút.');
        }

        // Đăng ký giữ chỗ cho user hiện tại
        $currentHolds[$userId] = [
            'seats' => $totalPersons,
            'expires_at' => now()->addMinutes(15)->timestamp,
        ];
        Cache::put($holdKey, $currentHolds, now()->addMinutes(15));

        // Nếu available_seats thực tế (ko tính hold) không đủ thì cũng báo lỗi
        if ($schedule->available_seats < $totalPersons) {
            return redirect()->back()->with('error', 'Tour không còn đủ chỗ trống cho số lượng hành khách này. Vui lòng chọn ngày khác.');
        }

        $holidaySurcharge = Holiday::getIncreasePercentage($schedule->departure_date);

        $basePrice = $schedule->tour->base_price;
        $childPrice = $schedule->tour->child_price ?? ($schedule->tour->base_price * 0.75);

        if ($holidaySurcharge > 0) {
            $basePrice = $basePrice * (1 + $holidaySurcharge / 100);
            $childPrice = $childPrice * (1 + $holidaySurcharge / 100);
        }

        $totalPrice = ($basePrice * $request->adults) + ($childPrice * $request->children);

        $user = Auth::user();
        $user->load('identity');
        $identity = $user->identity;

        $holidays = Holiday::all(['start_date', 'end_date', 'price_increase_percentage']);
        $tourCategoryIds = $schedule->tour->categories->pluck('id')->toArray();

        $coupons = Coupon::where(function ($query) {
            $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
        })
            ->where(function ($query) {
                $query->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->where(function ($query) use ($tourCategoryIds) {
                $query->whereNull('category_id')
                    ->orWhereIn('category_id', $tourCategoryIds);
            })
            ->get();

        return view('frontend.tours.checkout', [
            'schedule' => $schedule,
            'adults' => $request->adults,
            'children' => $request->children,
            'totalPersons' => $totalPersons,
            'totalPrice' => $totalPrice,
            'user' => $user,
            'identity' => $identity,
            'holidaySurcharge' => $holidaySurcharge,
            'basePrice' => $basePrice,
            'childPrice' => $childPrice,
            'holidays' => $holidays,
            'coupons' => $coupons,
        ]);
    }

    public function payWithVNPay(int $id, Request $request): RedirectResponse
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (in_array($booking->tour_status, [Booking::TOUR_COMPLETED, Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER])) {
            return redirect()->route('user.bookings')->with('error', 'Đơn hàng không thể thanh toán.');
        }

        $vnpayUrl = $this->vnPayService->generateUrl($booking, $request->ip());

        return redirect()->away($vnpayUrl);
    }

    public function vnpayReturn(Request $request): RedirectResponse
    {
        if (! $this->vnPayService->validateHash($request->all())) {
            return redirect()->route('user.bookings')->with('error', 'Chữ ký thanh toán không hợp lệ.');
        }

        $result = $this->vnPayService->processTransaction($request->all());

        if ($result['success']) {
            $booking = $result['booking'];
            if ($booking && $booking->transport_type === 'flight') {
                $this->flightService->bookFlightForBooking($booking);

                return redirect()->route('user.bookings')->with('success', 'Thanh toán VNPay thành công. Vé máy bay đã được đặt và gửi vào email của bạn.');
            }

            return redirect()->route('user.bookings')->with('success', 'Thanh toán đặt tour qua VNPay thành công!');
        } else {
            return redirect()->route('user.bookings')->with('error', 'Thanh toán không thành công. Mã lỗi: '.$result['responseCode']);
        }
    }

    public function vnpayIpn(Request $request): JsonResponse
    {
        if (! $this->vnPayService->validateHash($request->all())) {
            return response()->json([
                'RspCode' => '97',
                'Message' => 'Invalid signature',
            ]);
        }

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

        $this->vnPayService->processTransaction($request->all());

        return response()->json([
            'RspCode' => '00',
            'Message' => 'Confirm Success',
        ]);
    }
}
