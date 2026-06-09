<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = $credentials['login'];
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::query()->where($field, $login)->first();

        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            return back()->with('error', 'Account temporarily locked. Try again later.');
        }

        if (! $user || ! $user->is_active) {
            return back()->with('error', 'Invalid credentials or inactive account.');
        }

        if (! Auth::attempt([$field => $login, 'password' => $credentials['password']], $request->boolean('remember'))) {
            if ($user) {
                $user->increment('failed_login_attempts');
                if ($user->failed_login_attempts >= config('parking.max_login_attempts')) {
                    $user->update([
                        'locked_until' => now()->addMinutes(config('parking.lockout_minutes')),
                        'failed_login_attempts' => 0,
                    ]);
                }
            }

            return back()->with('error', 'Invalid credentials.');
        }

        $user->update(['failed_login_attempts' => 0, 'locked_until' => null]);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
