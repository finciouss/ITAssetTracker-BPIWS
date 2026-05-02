<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // ── Rate limiting: max 5 attempts per minute per IP + email ────────────
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            SystemLog::create([
                'user_email'  => $request->email,
                'action_type' => 'LoginBlocked',
                'description' => "Too many failed attempts from IP: {$request->ip()}. Blocked for {$seconds}s.",
                'timestamp'   => now(),
            ]);

            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey); // reset counter on success
            $request->session()->regenerate();

            SystemLog::create([
                'user_email'  => $request->email,
                'action_type' => 'Login',
                'description' => 'User authenticated successfully.',
                'timestamp'   => now(),
            ]);

            return redirect()->intended(route('dashboard'));
        }

        // Increment the failed attempt counter (60-second decay window)
        RateLimiter::hit($throttleKey, 60);

        SystemLog::create([
            'user_email'  => $request->email,
            'action_type' => 'LoginFailed',
            'description' => "Failed login attempt from IP: {$request->ip()}.",
            'timestamp'   => now(),
        ]);

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
