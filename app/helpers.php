<?php

use Illuminate\Support\Facades\Session;

if (! function_exists('format_currency')) {
    /**
     * Định dạng tiền tệ và chuyển đổi theo cấu hình cứng
     * Mặc định DB lưu giá VNĐ.
     */
    function format_currency($amount)
    {
        // Tỷ giá cứng
        $rates = [
            'VND' => 1,
            'USD' => 25000,
            'EUR' => 27000,
            'CNY' => 3500,
        ];

        // Lấy tiền tệ từ session, mặc định là VND
        $currency = Session::get('currency', 'VND');

        // Tính toán số tiền đã chuyển đổi
        $convertedAmount = $amount / ($rates[$currency] ?? 1);

        // Định dạng hiển thị
        switch ($currency) {
            case 'USD':
                return '$'.number_format($convertedAmount, 2);
            case 'EUR':
                return '€'.number_format($convertedAmount, 2);
            case 'CNY':
                return '¥'.number_format($convertedAmount, 2);
            case 'VND':
            default:
                return number_format($convertedAmount, 0, ',', '.').' VNĐ';
        }
    }
}
