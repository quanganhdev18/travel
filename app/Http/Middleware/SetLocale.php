<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Danh sách ngôn ngữ và tiền tệ hợp lệ.
     */
    private const VALID_LOCALES = ['vi', 'en', 'zh'];

    private const VALID_CURRENCIES = ['VND', 'USD', 'EUR', 'CNY'];

    /**
     * Handle an incoming request.
     *
     * Khôi phục ngôn ngữ và tiền tệ từ cookie vào session khi người dùng
     * quay lại trang web. Session có ưu tiên cao hơn cookie.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Khôi phục locale: ưu tiên session, sau đó cookie
        if (! Session::has('locale')) {
            $cookieLocale = $request->cookie('app_locale');
            if ($cookieLocale && in_array($cookieLocale, self::VALID_LOCALES)) {
                Session::put('locale', $cookieLocale);
            }
        }

        // Khôi phục currency: ưu tiên session, sau đó cookie
        if (! Session::has('currency')) {
            $cookieCurrency = $request->cookie('app_currency');
            if ($cookieCurrency && in_array($cookieCurrency, self::VALID_CURRENCIES)) {
                Session::put('currency', $cookieCurrency);
            }
        }

        // Áp dụng locale cho ứng dụng
        App::setLocale(Session::get('locale', config('app.locale')));

        return $next($request);
    }
}
