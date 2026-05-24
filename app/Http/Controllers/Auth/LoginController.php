<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // ─── Show Login Form ──────────────────────────────────────────────────────
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ─── Handle Login ─────────────────────────────────────────────────────────
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            // التحقق من أن المستخدم نشط
            if (Auth::user()->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => __('auth.account_suspended'),
                ])->onlyInput('email');
            }

            // تحديث آخر تسجيل دخول
            Auth::user()->update(['last_login_at' => now()]);

            // تعيين اللغة من تفضيلات المستخدم
            session(['locale' => Auth::user()->lang_preference ?? 'ar']);

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    // ─── Logout ───────────────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}