<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\FlightTicketMail;
use App\Mail\TourBookingMail;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Payment;
use App\Models\TourSchedule;
use App\Models\UserIdentity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
            'passengers' => 'required|array',
            'passengers.adult.*.full_name' => 'required|string|max:255',
            'passengers.adult.*.identity_number' => 'required|string|max:50',
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
            'payment_method' => 'required|in:cod,vnpay',
            'transport_price' => 'nullable|numeric',
            'transport_data' => 'nullable|string',
        ]);

        $user = Auth::user();
        if ($request->filled('customer_phone')) {
            $user->phone = $request->customer_phone;
            $user->save();
        }

        DB::beginTransaction();

        try {
            $totalPersons = $request->adults + $request->children;
            $schedule = TourSchedule::with('tour')->lockForUpdate()->find($request->schedule_id);

            if (! $schedule || $schedule->available_seats < $totalPersons) {
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

            $childPrice = $schedule->tour->child_price ?? ($schedule->tour->base_price * 0.75);
            $calculatedPrice = ($schedule->tour->base_price * $request->adults) + ($childPrice * $request->children);

            $transportPrice = $request->input('transport_price', 0);
            $finalTotalPrice = $calculatedPrice + $transportPrice;

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
            $booking->booking_status = 'pending';
            $booking->transport_type = $request->transport_type;
            $booking->transport_price = $transportPrice;
            $booking->transport_data = $transportData;
            $booking->save();

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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi đặt tour: '.$e->getMessage());

            return redirect()->back()->with('error', 'Đã có lỗi xảy ra trong quá trình đặt tour. Vui lòng thử lại.');
        }

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

        // Nếu thanh toán tiền mặt (COD), tiến hành xuất vé máy bay nếu chọn máy bay
        if ($booking->transport_type === 'flight') {
            $this->bookFlightForBooking($booking);
        }

        if ($request->transport_type === 'flight') {
            return redirect()->route('home')->with('success', 'Đặt tour và vé máy bay thành công. Vui lòng thanh toán sớm để giữ chỗ.');
        }

        if ($request->transport_type === 'bus') {
            return redirect()->route('home')->with('success', 'Đặt tour thành công. Chúng tôi sẽ liên hệ sớm để xác nhận chuyến xe.');
        }

        return redirect()->route('home')->with('success', 'Đặt tour thành công. Bạn tự túc phương tiện di chuyển.');
    }

    /**
     * Hàm gọi API xuất vé máy bay
     */
    private function bookFlightForBooking(Booking $booking)
    {
        if (! $booking->transport_data || ! isset($booking->transport_data['offer_id'])) {
            return false;
        }

        $offerId = $booking->transport_data['offer_id'];
        $primaryPassenger = $booking->booking_passengers()->where('passenger_type', 'adult')->first();
        if (! $primaryPassenger) {
            return false;
        }

        $names = explode(' ', $primaryPassenger->full_name);
        $familyName = array_pop($names);
        $givenName = implode(' ', $names) ?: $familyName;

        // Gọi API Duffel
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.env('DUFFEL_ACCESS_TOKEN'),
            'Duffel-Version' => 'v2',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://api.duffel.com/air/orders', [
            'data' => [
                'type' => 'instant',
                'selected_offers' => [$offerId],
                'passengers' => [
                    [
                        'id' => $offerId, // API thường map hành khách hoặc bỏ qua tuỳ version. Gửi kèm thông tin.
                        'family_name' => $familyName,
                        'given_name' => $givenName,
                        'phone_number' => str_replace(' ', '', $booking->user->phone ?? '+84999999999'),
                        'email' => $booking->user->email,
                        'born_on' => $primaryPassenger->date_of_birth,
                        'gender' => $primaryPassenger->gender === 'male' ? 'm' : 'f',
                    ],
                ],
                'payments' => [
                    [
                        'type' => 'balance',
                        'amount' => strval($booking->transport_price),
                        'currency' => 'VND',
                    ],
                ],
            ],
        ]);

        if ($response->successful()) {
            $bookingRef = $response->json()['data']['booking_reference'] ?? null;
            if ($bookingRef) {
                $booking->pnr_code = $bookingRef;
                $booking->save();

                try {
                    Mail::to($booking->user->email)->send(
                        new FlightTicketMail($booking, $bookingRef, $primaryPassenger->full_name)
                    );
                } catch (\Exception $e) {
                    Log::error('Lỗi gửi mail vé máy bay: '.$e->getMessage());
                }

                return true;
            }
        } else {
            Log::error('Lỗi book vé Duffel: '.$response->body());
        }

        return false;
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

        if ($schedule->available_seats < $totalPersons) {
            return redirect()->back()->with('error', 'Tour không còn đủ chỗ trống cho số lượng hành khách này. Vui lòng chọn ngày khác.');
        }

        $childPrice = $schedule->tour->child_price ?? ($schedule->tour->base_price * 0.75);
        $totalPrice = ($schedule->tour->base_price * $request->adults) + ($childPrice * $request->children);

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
                    $this->bookFlightForBooking($booking);

                    return redirect()->route('user.bookings')->with('success', 'Thanh toán VNPay thành công. Vé máy bay đã được đặt và gửi vào email của bạn.');
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
