<?php

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

describe('Locale switching', function () {
    it('saves locale to session and queues a cookie', function () {
        $response = $this->get(route('locale.switch', ['locale' => 'en']));

        $response->assertRedirect();
        expect(Session::get('locale'))->toBe('en');
        expect($response->headers->getCookies())->not->toBeEmpty();

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($c) => $c->getName() === 'app_locale');

        // Cookie tồn tại và có giá trị (Laravel mã hóa cookie nên không so sánh plaintext)
        expect($cookie)->not->toBeNull();
        expect($cookie->getValue())->toBeString()->not->toBeEmpty();
    });

    it('rejects an invalid locale', function () {
        $response = $this->get(route('locale.switch', ['locale' => 'fr']));

        $response->assertRedirect();
        expect(Session::has('locale'))->toBeFalse();
    });
});

describe('Currency switching', function () {
    it('saves currency to session and queues a cookie', function () {
        $response = $this->get(route('currency.switch', ['currency' => 'USD']));

        $response->assertRedirect();
        expect(Session::get('currency'))->toBe('USD');

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($c) => $c->getName() === 'app_currency');

        // Cookie tồn tại và có giá trị (Laravel mã hóa cookie nên không so sánh plaintext)
        expect($cookie)->not->toBeNull();
        expect($cookie->getValue())->toBeString()->not->toBeEmpty();
    });

    it('rejects an invalid currency', function () {
        $response = $this->get(route('currency.switch', ['currency' => 'BTC']));

        $response->assertRedirect();
        expect(Session::has('currency'))->toBeFalse();
    });
});

describe('SetLocale middleware', function () {
    it('restores locale from cookie into session when session is empty', function () {
        $response = $this->withCookie('app_locale', 'zh')->get('/');

        $response->assertOk();
        expect(Session::get('locale'))->toBe('zh');
    });

    it('restores currency from cookie into session when session is empty', function () {
        $response = $this->withCookie('app_currency', 'EUR')->get('/');

        $response->assertOk();
        expect(Session::get('currency'))->toBe('EUR');
    });

    it('does not override session locale with cookie value', function () {
        Session::put('locale', 'en');

        $response = $this->withCookie('app_locale', 'zh')->get('/');

        $response->assertOk();
        expect(Session::get('locale'))->toBe('en');
    });

    it('ignores invalid locale from cookie', function () {
        $response = $this->withCookie('app_locale', 'xx')->get('/');

        $response->assertOk();
        expect(Session::has('locale'))->toBeFalse();
    });
});
