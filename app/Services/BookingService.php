<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ParkingSlot;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BookingService
{
    public function __construct(
        protected ParkingAvailabilityService $availability,
    ) {}

    public function create(
        User $user,
        ParkingSlot $slot,
        Carbon $startsAt,
        ?Carbon $endsAt,
        ?int $vehicleId = null,
    ): Booking {
        if ($slot->is_disabled) {
            throw new InvalidArgumentException('This slot is not available for booking.');
        }

        $endsAt ??= $startsAt->copy()->addHours(2);

        if ($this->availability->hasOverlap($slot->id, $startsAt, $endsAt)) {
            throw new InvalidArgumentException('This slot is already reserved for the selected time.');
        }

        return DB::transaction(function () use ($user, $slot, $startsAt, $endsAt, $vehicleId) {
            $booking = Booking::query()->create([
                'user_id' => $user->id,
                'parking_slot_id' => $slot->id,
                'vehicle_id' => $vehicleId,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => 'pending',
                'expires_at' => now()->addMinutes(Setting::bookingHoldMinutes()),
            ]);

            $slot->update(['status' => 'reserved']);

            return $booking->fresh(['parkingSlot.zone', 'vehicle', 'user']);
        });
    }

    public function confirm(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            $booking->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'expires_at' => null,
            ]);

            return $booking->fresh();
        });
    }

    public function cancel(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $this->releaseSlotIfPossible($booking);

            return $booking->fresh();
        });
    }

    public function markActive(Booking $booking): Booking
    {
        $booking->update(['status' => 'active']);

        $booking->parkingSlot?->update(['status' => 'occupied']);

        return $booking->fresh();
    }

    public function markCompleted(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'completed', 'ends_at' => $booking->ends_at ?? now()]);

            $this->releaseSlotIfPossible($booking);

            return $booking->fresh();
        });
    }

    public function expirePending(): int
    {
        $expired = Booking::query()
            ->where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $booking) {
            DB::transaction(function () use ($booking) {
                $booking->update(['status' => 'expired']);
                $this->releaseSlotIfPossible($booking);
            });
        }

        return $expired->count();
    }

    protected function releaseSlotIfPossible(Booking $booking): void
    {
        $slot = $booking->parkingSlot;

        if (! $slot) {
            return;
        }

        $hasActive = Booking::query()
            ->where('parking_slot_id', $slot->id)
            ->where('id', '!=', $booking->id)
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->exists();

        if (! $hasActive) {
            $slot->update(['status' => 'free']);
        }
    }
}
