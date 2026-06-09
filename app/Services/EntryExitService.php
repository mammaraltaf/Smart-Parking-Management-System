<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\EntryExitLog;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class EntryExitService
{
    public function __construct(
        protected BookingService $bookings,
    ) {}

    public function verifyQr(string $qrToken, string $gateId, string $direction): EntryExitLog
    {
        $booking = Booking::query()
            ->with(['vehicle', 'parkingSlot', 'user'])
            ->where('qr_token', $qrToken)
            ->whereIn('status', ['confirmed', 'active', 'pending'])
            ->first();

        if (! $booking) {
            return $this->logDenied($gateId, $direction, 'qr', null, null, 'Invalid or expired booking QR.');
        }

        if ($booking->status === 'pending') {
            $this->bookings->confirm($booking);
            $booking->refresh();
        }

        if ($direction === 'entry') {
            if ($booking->status === 'confirmed') {
                $this->bookings->markActive($booking);
            }

            return $this->logAllowed($gateId, $direction, 'qr', $booking, $booking->vehicle?->license_plate);
        }

        if ($direction === 'exit') {
            $log = $this->logAllowed($gateId, $direction, 'qr', $booking, $booking->vehicle?->license_plate);
            $this->bookings->markCompleted($booking);

            return $log;
        }

        throw new InvalidArgumentException('Direction must be entry or exit.');
    }

    public function verifyPlate(string $plate, string $gateId, string $direction): EntryExitLog
    {
        $normalized = strtoupper(preg_replace('/\s+/', '', $plate));
        $vehicle = Vehicle::query()->whereRaw("UPPER(REPLACE(license_plate, ' ', '')) = ?", [$normalized])->first();

        if (! $vehicle) {
            return $this->logDenied($gateId, $direction, 'lpr', null, $normalized, 'Unregistered vehicle.');
        }

        $booking = Booking::query()
            ->where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['confirmed', 'active', 'pending'])
            ->latest('starts_at')
            ->first();

        if (! $booking) {
            return $this->logDenied($gateId, $direction, 'lpr', null, $normalized, 'No active booking for vehicle.');
        }

        if ($direction === 'entry') {
            if ($booking->status !== 'active') {
                $this->bookings->markActive($booking);
            }

            return $this->logAllowed($gateId, $direction, 'lpr', $booking, $normalized);
        }

        $log = $this->logAllowed($gateId, $direction, 'lpr', $booking, $normalized);
        $this->bookings->markCompleted($booking);

        return $log;
    }

    protected function logAllowed(
        string $gateId,
        string $direction,
        string $method,
        Booking $booking,
        ?string $plate,
    ): EntryExitLog {
        return DB::transaction(function () use ($gateId, $direction, $method, $booking, $plate) {
            return EntryExitLog::query()->create([
                'gate_id' => $gateId,
                'direction' => $direction,
                'vehicle_id' => $booking->vehicle_id,
                'booking_id' => $booking->id,
                'license_plate_guess' => $plate,
                'verification_method' => $method,
                'result' => 'allowed',
                'occurred_at' => now(),
            ]);
        });
    }

    protected function logDenied(
        string $gateId,
        string $direction,
        string $method,
        ?Booking $booking,
        ?string $plate,
        string $reason = 'Denied',
    ): EntryExitLog {
        return EntryExitLog::query()->create([
            'gate_id' => $gateId,
            'direction' => $direction,
            'vehicle_id' => $booking?->vehicle_id,
            'booking_id' => $booking?->id,
            'license_plate_guess' => $plate,
            'verification_method' => $method,
            'result' => 'denied',
            'occurred_at' => now(),
            'meta' => ['reason' => $reason],
        ]);
    }
}
