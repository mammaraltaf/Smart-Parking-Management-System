<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function show(): View
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $data = $request->validate(['login' => ['required', 'string']]);
        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::query()->where($field, $data['login'])->first();

        if (! $user) {
            return back()->with('error', 'No account found for that email or phone.');
        }

        $otp = (string) random_int(100000, 999999);
        session([
            'password_reset_user_id' => $user->id,
            'password_reset_otp' => $otp,
            'password_reset_expires' => now()->addMinutes(10)->timestamp,
        ]);

        return redirect()->route('password.reset')
            ->with('success', "OTP sent (demo): {$otp} — configure mail in production.");
    }

    public function showReset(): View
    {
        return view('auth.reset-password');
    }

    public function reset(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (! session('password_reset_user_id') || session('password_reset_otp') !== $data['otp']) {
            return back()->with('error', 'Invalid OTP.');
        }

        if (now()->timestamp > (int) session('password_reset_expires', 0)) {
            return back()->with('error', 'OTP expired. Request a new one.');
        }

        $user = User::query()->findOrFail(session('password_reset_user_id'));
        $user->update(['password' => $data['password']]);
        session()->forget(['password_reset_user_id', 'password_reset_otp', 'password_reset_expires']);

        return redirect()->route('login')->with('success', 'Password updated. You can sign in now.');
    }
}
