<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // Generate math CAPTCHA
        $a = rand(1, 9);
        $b = rand(1, 9);
        $request->session()->put('captcha_answer', $a + $b);

        $availableYears = config('sipantau.available_years', [date('Y')]);
        $defaultYear = config('sipantau.default_year', date('Y'));

        return view('auth.login', compact('a', 'b', 'availableYears', 'defaultYear'));
    }

    public function login(Request $request)
    {
        // Rate limiting: max 5 attempts per minute per IP
        $throttleKey = 'login|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ])->withInput($request->only('username', 'tahun'));
        }

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'captcha' => 'required|integer',
            'tahun' => 'required|integer',
        ]);

        // Validate year is in the allowed list
        $availableYears = config('sipantau.available_years', []);
        if (!in_array((int) $request->tahun, $availableYears, true)) {
            return back()->withErrors([
                'username' => 'Tahun yang dipilih tidak valid.',
            ])->withInput($request->only('username', 'tahun'));
        }

        // Validate CAPTCHA
        $expectedAnswer = $request->session()->get('captcha_answer');
        if ((int) $request->captcha !== (int) $expectedAnswer) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'captcha' => 'Jawaban verifikasi keamanan salah.',
            ])->withInput($request->only('username', 'tahun'));
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Simpan tahun yang dipilih ke session
            $request->session()->put('selected_year', (int) $request->tahun);

            // Redirect berdasarkan role
            $user = auth()->user();

            if ($user->isSuperadmin() || $user->isKasubagPk()) {
                return redirect()->route('superadmin.dashboard');
            }

            if ($user->isCamat()) {
                return redirect()->route('camat.dashboard');
            }

            // PPTK
            return redirect()->route('pptk.dokumen.index');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->only('username', 'tahun'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
