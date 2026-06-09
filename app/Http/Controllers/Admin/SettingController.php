<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'hourly_rate' => Setting::hourlyRate(),
            'late_fee_per_hour' => Setting::lateFeePerHour(),
            'booking_hold_minutes' => Setting::bookingHoldMinutes(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hourly_rate' => ['required', 'numeric', 'min:0'],
            'late_fee_per_hour' => ['required', 'numeric', 'min:0'],
            'booking_hold_minutes' => ['required', 'integer', 'min:5', 'max:120'],
        ]);

        Setting::set('hourly_rate', $data['hourly_rate']);
        Setting::set('late_fee_per_hour', $data['late_fee_per_hour']);
        Setting::set('booking_hold_minutes', $data['booking_hold_minutes']);

        return back()->with('success', 'Settings saved.');
    }
}
