<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class VnPayService
{
    /**
     * Generate VNPAY payment URL for a given booking.
     */
    public function generateUrl(Booking $booking, string $ipAddress): string
    {
        $vnp_TmnCode = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_Url = config('vnpay.url');
        $vnp_Returnurl = route('frontend.tours.vnpay_return');

        $vnp_TxnRef = $booking->id.'_'.time();
        $vnp_OrderInfo = 'Thanh toan dat tour #'.str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT);
        $vnp_OrderType = 'billpayment';

        // Xác định số tiền cần thanh toán
        if ($booking->payment_type === 'deposit' && $booking->payment_status === Booking::PAYMENT_PAID_30) {
            $actualAmount = $booking->total_price * 0.7;
            $vnp_OrderInfo = 'Thanh toan phan con lai tour #'.str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT);
        } elseif ($booking->payment_type === 'deposit') {
            $actualAmount = $booking->total_price * 0.3;
        } else {
            $actualAmount = $booking->total_price;
        }

        $vnp_Amount = (int) ($actualAmount * 100);
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
            'amount' => $actualAmount,
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

    /**
     * Validate the VNPAY hash from a request.
     */
    public function validateHash(array $requestData): bool
    {
        $vnp_SecureHash = $requestData['vnp_SecureHash'] ?? '';
        $vnp_HashSecret = config('vnpay.hash_secret');

        $inputData = [];
        foreach ($requestData as $key => $value) {
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

        return $secureHash === $vnp_SecureHash;
    }

    /**
     * Process a VNPAY transaction response (from return URL or IPN).
     */
    public function processTransaction(array $requestData): array
    {
        $txnRef = $requestData['vnp_TxnRef'] ?? '';
        $parts = explode('_', $txnRef);
        $bookingId = $parts[0] ?? null;

        $booking = Booking::with('tour_schedule.tour')->find($bookingId);
        $payment = Payment::where('transaction_code', $txnRef)->first();

        $isSuccess = ($requestData['vnp_ResponseCode'] ?? '') === '00';

        if ($payment) {
            $payment->update([
                'payment_status' => $isSuccess ? 'success' : 'failed',
                'paid_at' => $isSuccess ? now() : null,
            ]);
        }

        if ($isSuccess && $booking) {
            $newPaidAmount = $booking->paid_amount + ($payment ? $payment->amount : 0);

            if ($booking->payment_type === 'deposit' && $booking->payment_status === Booking::PAYMENT_PENDING) {
                $newPaymentStatus = Booking::PAYMENT_PAID_30;
            } else {
                $newPaymentStatus = Booking::PAYMENT_PAID_100;
            }

            $booking->update([
                'payment_status' => $newPaymentStatus,
                'paid_amount' => $newPaidAmount,
                'booking_status' => 'confirmed',
            ]);
        }

        return [
            'success' => $isSuccess,
            'booking' => $booking,
            'payment' => $payment,
            'responseCode' => $requestData['vnp_ResponseCode'] ?? '',
            'amount' => isset($requestData['vnp_Amount']) ? $requestData['vnp_Amount'] / 100 : 0,
        ];
    }
}
