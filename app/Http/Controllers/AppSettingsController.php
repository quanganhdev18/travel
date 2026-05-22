<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class AppSettingsController extends Controller
{
    public function switchLocale($locale)
    {
        $locales = ['vi', 'en', 'zh'];
        if (in_array($locale, $locales)) {
            Session::put('locale', $locale);
            App::setLocale($locale);
        }

        return back();
    }

    public function switchCurrency($currency)
    {
        $currencies = ['VND', 'USD', 'EUR', 'CNY'];
        if (in_array($currency, $currencies)) {
            Session::put('currency', $currency);
        }

        return back();
    }
}
