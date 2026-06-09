<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;

class BillingService
{
    public function calculateForBooking(Booking $booking, ?Carbon $exitAt = null): array
    {
        $entry = $booking->entryExitLogs()->where('direction', 'entry')->where('result', 'allowed')->oldest('occurred_at')->first();
        $exit = $booking->entryExitLogs()->where('direction', 'exit')->where('result', 'allowed')->oldest('occurred_at')->first();

        $startsAt = $entry?->occurred_at ?? $booking->starts_at;
        $endsAt = $exit?->occurred_at ?? $exitAt ?? now();

        $minutes = max(1, $startsAt->diffInMinutes($endsAt));
        $hours = ceil($minutes / 60);
        $rate = $booking->parkingSlot?->hourly_rate ?? Setting::hourlyRate();
        $base = round($hours * (float) $rate, 2);

        $lateFee = 0.0;
        if ($booking->ends_at && $endsAt->gt($booking->ends_at->copy()->addMinutes(config('parking.grace_minutes')))) {
            $overHours = ceil($endsAt->diffInMinutes($booking->ends_at) / 60);
            $lateFee = round($overHours * Setting::lateFeePerHour(), 2);
        }

        return [
            'minutes' => $minutes,
            'hours' => $hours,
            'hourly_rate' => (float) $rate,
            'base_amount' => $base,
            'late_fee' => $lateFee,
            'total' => $base + $lateFee,
        ];
    }

    public function recordPayment(Booking $booking, string $method, float $amount, float $lateFee = 0): Payment
    {
        return Payment::query()->create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'method' => $method,
            'amount' => $amount,
            'late_fee' => $lateFee,
            'status' => 'completed',
            'reference' => 'PAY-'.strtoupper(uniqid()),
        ]);
    }
}
