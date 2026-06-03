<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class AppSettingsController extends Controller
{
    /**
     * Thời gian lưu cookie: 1 năm (tính bằng phút).
     */
    private const COOKIE_LIFETIME_MINUTES = 60 * 24 * 365; // 1 năm

    public function switchLocale(string $locale): RedirectResponse
    {
        $locales = ['vi', 'en', 'zh'];
        if (in_array($locale, $locales)) {
            Session::put('locale', $locale);
            App::setLocale($locale);

            // Lưu vào cookie 1 năm để duy trì khi quay lại trang
            Cookie::queue('app_locale', $locale, self::COOKIE_LIFETIME_MINUTES);
        }

        return back();
    }

    public function switchCurrency(string $currency): RedirectResponse
    {
        $currencies = ['VND', 'USD', 'EUR', 'CNY'];
        if (in_array($currency, $currencies)) {
            Session::put('currency', $currency);

            // Lưu vào cookie 1 năm để duy trì khi quay lại trang
            Cookie::queue('app_currency', $currency, self::COOKIE_LIFETIME_MINUTES);
        }

        return back();
    }
}
