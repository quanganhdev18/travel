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
use Illuminate\Support\Facades\DB;
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

        $maximumBookerDateOfBirth = Carbon::today()
            ->subYears(18)
            ->format('Y-m-d');
        $request->validate([
            'schedule_id' => 'required|exists:tour_schedules,id',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email|max:255',
            'meeting_point' => 'nullable|string|max:255',
            'passengers' => 'required|array',
            'passengers.adult.*.full_name' => 'required|string|max:255',
            'passengers.adult.*.identity_number' => 'required|string|max:50',
            'passengers.adult.0.date_of_birth' => [
                'required',
                'date',
                'before_or_equal:'.$maximumBookerDateOfBirth,
            ],
            'passengers.adult.*.date_of_birth' => 'required|date',
            'passengers.adult.*.gender' => 'required|in:male,female,other',
            'passengers.child.*.full_name' => 'nullable|string|max:255',
            'passengers.child.*.date_of_birth' => 'nullable|date',
            'passengers.child.*.gender' => 'nullable|in:male,female,other',
            'total_price' => 'required|numeric',
            'transport_type' => 'required|in:flight,bus,self',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'issue_place' => 'nullable|string|max:255',
            'front_image' => 'nullable|image|max:5120',
            'back_image' => 'nullable|image|max:5120',
            'payment_type' => 'required|in:full,deposit',
            'payment_method' => 'required|in:transfer,vnpay',
            'transport_price' => 'nullable|numeric',
            'transport_data' => 'nullable|string',
        ], [
            'passengers.adult.0.date_of_birth.required' => 'Vui lòng nhập ngày sinh của người đặt tour.',

            'passengers.adult.0.date_of_birth.date' => 'Ngày sinh của người đặt tour không hợp lệ.',

            'passengers.adult.0.date_of_birth.before_or_equal' => 'Người đặt tour phải đủ 18 tuổi trở lên mới được phép đặt tour.',
        ]);

        $user = Auth::user();
        $sessionId = session()->getId();

        try {

            $booking = $this->bookingService->createBooking($request->validated(), $user, $sessionId);
            $schedule = $booking->tour_schedule;
        } catch (Exception $e) {

            $totalPersons = $request->adults + $request->children;
            $schedule = TourSchedule::with('tour')->lockForUpdate()->find($request->schedule_id);

            if (! $schedule || Carbon::parse($schedule->departure_date)->lt(Carbon::today()->addDays(4))) {
                DB::rollBack();

                return redirect()->back()->with('error', 'Tour khởi hành trong vòng 3 ngày tới không thể đặt trực tuyến. Vui lòng chọn lịch trình khác.');
            }

            if ($schedule->available_seats < $totalPersons) {
                DB::rollBack();

                return redirect()->back()->with('error', 'Tour không còn đủ chỗ trống cho số lượng hành khách này. Vui lòng chọn ngày khác.');
            }

            $identity = UserIdentity::where('user_id', $user->id)->first();

            if (! $identity) {
                $identity = new UserIdentity;
                $identity->user_id = $user->id;
            } else {
                $primaryIdentityNumber = $request->passengers['adult'][0]['identity_number'] ?? null;
                $existingIdentity = UserIdentity::where('identity_number', $primaryIdentityNumber)
                    ->where('user_id', '!=', $user->id)
                    ->first();

                if ($existingIdentity) {
                    DB::rollBack();

                    return redirect()->back()
                        ->with('error', 'Số CCCD/Hộ chiếu này đã được đăng ký bởi người dùng khác. Vui lòng kiểm tra lại.');
                }
            }

            $primaryAdult = $request->passengers['adult'][0] ?? null;
            if ($primaryAdult) {
                $identity->full_name = $primaryAdult['full_name'];
                $identity->identity_number = $primaryAdult['identity_number'];
                $identity->date_of_birth = $primaryAdult['date_of_birth'];
                $identity->gender = $primaryAdult['gender'];
                $identity->issue_date = $request->issue_date ?? '2020-01-01';
                $identity->expiry_date = $request->expiry_date ?? '2040-01-01';
                $identity->issue_place = $request->issue_place ?? 'Hà Nội';

                if ($request->hasFile('front_image')) {
                    $frontPath = $request->file('front_image')->store('identities', 'public');
                    $identity->front_image_url = '/storage/'.$frontPath;
                }

                if ($request->hasFile('back_image')) {
                    $backPath = $request->file('back_image')->store('identities', 'public');
                    $identity->back_image_url = '/storage/'.$backPath;
                }

                $identity->save();
            }

            $holidaySurcharge = Holiday::getIncreasePercentage($schedule->departure_date);

            $basePrice = $schedule->tour->base_price;
            $childPrice = $schedule->tour->child_price ?? ($schedule->tour->base_price * 0.75);

            if ($holidaySurcharge > 0) {
                $basePrice = $basePrice * (1 + $holidaySurcharge / 100);
                $childPrice = $childPrice * (1 + $holidaySurcharge / 100);
            }

            $calculatedPrice = ($basePrice * $request->adults) + ($childPrice * $request->children);

            $transportPrice = $request->input('transport_price', 0);

            // Tính tiền vé tham quan
            $ticketPrice = 0;
            $selectedTickets = [];
            if ($request->filled('tickets') && is_array($request->tickets)) {
                foreach ($request->tickets as $ticketOptionId => $qty) {
                    if ($qty > 0) {
                        $opt = TicketOption::find($ticketOptionId);
                        if ($opt) {
                            $ticketPrice += $opt->price * $qty;
                            $selectedTickets[] = [
                                'option' => $opt,
                                'qty' => $qty,
                            ];
                        }
                    }
                }
            }

            // Tính tiền Addons
            $addonPriceTotal = 0;
            $selectedAddons = [];
            if ($request->filled('addons') && is_array($request->addons)) {
                foreach ($request->addons as $addonId => $data) {
                    $qty = isset($data['qty']) ? (int) $data['qty'] : 0;
                    if ($qty > 0) {
                        $addon = Addon::find($addonId);
                        if ($addon) {
                            $usageDate = $data['usage_date'] ?? $schedule->departure_date;
                            $addonSurcharge = Holiday::getIncreasePercentage($usageDate);
                            $price = $addon->price * (1 + $addonSurcharge / 100);

                            $addonPriceTotal += $price * $qty;
                            $selectedAddons[] = [
                                'addon_id' => $addon->id,
                                'addon_name' => $addon->name,
                                'price' => $price,
                                'quantity' => $qty,
                                'usage_date' => $usageDate,
                            ];
                        }
                    }
                }
            }

            $finalTotalPrice = $calculatedPrice + $transportPrice + $ticketPrice + $addonPriceTotal;
            $discountAmount = 0;
            $couponId = null;

            if ($request->filled('coupon_code')) {
                $tourCategoryIds = $schedule->tour->categories->pluck('id')->toArray();
                $coupon = Coupon::where('code', $request->coupon_code)
                    ->where(function ($query) {
                        $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
                    })
                    ->where(function ($query) {
                        $query->whereNull('valid_from')->orWhere('valid_from', '<=', now());
                    })
                    ->where(function ($query) use ($tourCategoryIds) {
                        $query->whereNull('category_id')
                            ->orWhereIn('category_id', $tourCategoryIds);
                    })
                    ->first();

                if ($coupon && $finalTotalPrice >= $coupon->min_order_value) {
                    if ($coupon->usage_limit === null || $coupon->used_count < $coupon->usage_limit) {
                        $discount = 0;
                        if ($coupon->discount_type === 'percent') {
                            $discount = $finalTotalPrice * ($coupon->discount_value / 100);
                            if ($coupon->max_discount) {
                                $discount = min($discount, $coupon->max_discount);
                            }
                        } else {
                            $discount = $coupon->discount_value;
                        }
                        $discountAmount = $discount;
                        $couponId = $coupon->id;
                        $finalTotalPrice = max(0, $finalTotalPrice - $discountAmount);

                        $coupon->increment('used_count');
                    }
                }
            }

            $transportData = null;
            if ($request->filled('transport_data')) {
                $transportData = json_decode($request->transport_data, true);
            }

            $booking = new Booking;
            $booking->user_id = $user->id;
            $booking->tour_schedule_id = $request->schedule_id;
            $booking->adults_count = $request->adults;
            $booking->children_count = $request->children;
            $booking->total_price = $finalTotalPrice;
            $booking->discount_amount = $discountAmount;
            $booking->coupon_id = $couponId;
            $booking->payment_status = Booking::PAYMENT_PENDING;
            $booking->tour_status = Booking::TOUR_UPCOMING;
            $booking->transport_type = $request->transport_type;
            $booking->meeting_point = $request->meeting_point;
            $booking->transport_price = $transportPrice;
            $booking->transport_data = $transportData;
            $booking->payment_type = $request->payment_type ?? 'full';
            $booking->payment_method = $request->payment_method ?? 'transfer';
            $booking->paid_amount = 0;
            $booking->is_passenger_list_submitted = ($totalPersons < 2);
            $booking->save();

            // Lưu TicketBooking
            foreach ($selectedTickets as $item) {
                $tb = new TicketBooking;
                $tb->user_id = $user->id;
                $tb->booking_id = $booking->id; // Khóa ngoại mới thêm
                $tb->ticket_option_id = $item['option']->id;
                $tb->quantity = $item['qty'];
                $tb->total_price = $item['option']->price * $item['qty'];
                $tb->visit_date = $schedule->departure_date; // Mặc định dùng ngày khởi hành tour
                $tb->booking_status = 'pending';
                $tb->save();
            }

            // Lưu Addons
            foreach ($selectedAddons as $item) {
                BookingAddon::create([
                    'booking_id' => $booking->id,
                    'addon_id' => $item['addon_id'],
                    'addon_name' => $item['addon_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'usage_date' => $item['usage_date'],
                ]);
            }

            if (isset($request->passengers['adult'])) {
                foreach ($request->passengers['adult'] as $adult) {
                    $passenger = new BookingPassenger;
                    $passenger->booking_id = $booking->id;
                    $passenger->full_name = $adult['full_name'];
                    $passenger->date_of_birth = $adult['date_of_birth'];
                    $passenger->identity_number = $adult['identity_number'] ?? null;
                    $passenger->gender = $adult['gender'];
                    $passenger->passenger_type = 'adult';
                    $passenger->save();
                }
            }

            if (isset($request->passengers['child'])) {
                foreach ($request->passengers['child'] as $child) {
                    $passenger = new BookingPassenger;
                    $passenger->booking_id = $booking->id;
                    $passenger->full_name = $child['full_name'];
                    $passenger->date_of_birth = $child['date_of_birth'];
                    $passenger->gender = $child['gender'];
                    $passenger->passenger_type = 'child';
                    $passenger->save();
                }
            }

            $schedule->available_seats -= $totalPersons;
            $schedule->save();

            // Release Cache Hold
            $holdKey = "tour_schedule_{$schedule->id}_holds";
            $currentHolds = Cache::get($holdKey, []);
            $userId = Auth::id() ?? session()->getId();
            if (isset($currentHolds[$userId])) {
                unset($currentHolds[$userId]);
                Cache::put($holdKey, $currentHolds, now()->addMinutes(15));
            }

            DB::commit();

            // Phát sóng event cập nhật chỗ trống
            broadcast(new SeatAvailabilityUpdated($schedule->id, $schedule->available_seats))->toOthers();
        } catch (Exception $e) {
            DB::rollBack();

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

        if ($schedule->status !== 'available' || Carbon::parse($schedule->departure_date)->lt(Carbon::today()->addDays(4))) {
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

    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'order_value' => 'required|numeric|min:0',
            'schedule_id' => 'required|exists:tour_schedules,id',
        ]);

        $coupon = Coupon::where('code', $request->code)
            ->where(function ($query) {
                $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn.',
            ], 404);
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết lượt sử dụng.',
            ], 400);
        }

        $schedule = TourSchedule::with('tour.categories')->find($request->schedule_id);
        $tourCategoryIds = $schedule->tour->categories->pluck('id')->toArray();

        if ($coupon->category_id !== null && ! in_array($coupon->category_id, $tourCategoryIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không áp dụng cho loại tour này.',
            ], 400);
        }

        if ($request->order_value < $coupon->min_order_value) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu '.format_currency($coupon->min_order_value),
            ], 400);
        }

        $discount = 0;
        if ($coupon->discount_type === 'percent') {
            $discount = $request->order_value * ($coupon->discount_value / 100);
            if ($coupon->max_discount) {
                $discount = min($discount, $coupon->max_discount);
            }
        } else {
            $discount = $coupon->discount_value;
        }

        $discount = min($discount, $request->order_value);
        $finalPrice = max(0, $request->order_value - $discount);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount_amount' => $discount,
            'final_price' => $finalPrice,
        ]);
    }
}
