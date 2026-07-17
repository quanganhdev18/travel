<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Hiển thị form đăng nhập.
     */
    public function create(Request $request): View
    {
        $this->storeRedirectUrl($request);

        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $this->storeRedirectUrl($request);

        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 'admin' || $user->role === 'staff') {
            return redirect('/admin/dashboard');
        }

        if ($user->role === 'cskh') {
            return redirect()->route('admin.chat.index');
        }

        if ($user->role === 'guide') {
            return redirect()->route('guide.dashboard');
        }

        return redirect()->intended('/');
    }

    /**
     * Lưu lại URL người dùng đang đứng trước khi đăng nhập.
     */
    private function storeRedirectUrl(Request $request): void
    {
        $redirectUrl = $request->query('redirect') ?? $request->input('redirect');

        if (!$redirectUrl) {
            return;
        }

        $appUrl = url('/');

        if (
            Str::startsWith($redirectUrl, $appUrl) ||
            Str::startsWith($redirectUrl, '/')
        ) {
            session(['url.intended' => $redirectUrl]);
        }
    }

    /**
     * Đăng xuất.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
