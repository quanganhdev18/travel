<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;

class CookieConsentController extends Controller
{
    /**
     * Thời gian lưu cookie đồng ý: 1 năm (tính bằng phút).
     */
    private const CONSENT_ACCEPTED_LIFETIME = 60 * 24 * 365;

    /**
     * Thời gian lưu cookie từ chối: 30 ngày (tính bằng phút).
     * Đủ để không hiện lại banner nhưng sẽ hỏi lại sau 30 ngày.
     */
    private const CONSENT_DECLINED_LIFETIME = 60 * 24 * 30;

    /**
     * Người dùng đồng ý sử dụng cookie.
     * Lưu cookie consent=accepted để cho phép ghi cookie tùy chọn.
     */
    public function accept(): RedirectResponse
    {
        Cookie::queue('cookie_consent', 'accepted', self::CONSENT_ACCEPTED_LIFETIME);

        return back();
    }

    /**
     * Người dùng từ chối cookie.
     * Lưu cookie consent=declined để không hiện lại banner (30 ngày).
     * Dữ liệu tùy chọn (locale, currency) chỉ lưu ở session.
     */
    public function decline(): RedirectResponse
    {
        Cookie::queue('cookie_consent', 'declined', self::CONSENT_DECLINED_LIFETIME);

        return back();
    }
}
