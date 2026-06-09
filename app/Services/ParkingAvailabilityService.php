<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ParkingSlot;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ParkingAvailabilityService
{
    public function availableSlots(
        ?int $zoneId = null,
        ?string $type = null,
        ?string $size = null,
        ?Carbon $startsAt = null,
        ?Carbon $endsAt = null,
    ): Collection {
        $startsAt ??= now();
        $endsAt ??= $startsAt->copy()->addHours(2);

        $query = ParkingSlot::query()
            ->with('zone')
            ->where('is_disabled', false)
            ->whereIn('status', ['free', 'reserved']);

        if ($zoneId) {
            $query->where('zone_id', $zoneId);
        }
        if ($type) {
            $query->where('type', $type);
        }
        if ($size) {
            $query->where('size', $size);
        }

        return $query->get()->filter(function (ParkingSlot $slot) use ($startsAt, $endsAt) {
            return ! $this->hasOverlap($slot->id, $startsAt, $endsAt);
        })->values();
    }

    public function hasOverlap(int $slotId, Carbon $startsAt, Carbon $endsAt, ?int $ignoreBookingId = null): bool
    {
        return Booking::query()
            ->where('parking_slot_id', $slotId)
            ->when($ignoreBookingId, fn (Builder $q) => $q->where('id', '!=', $ignoreBookingId))
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where(function (Builder $q) use ($startsAt, $endsAt) {
                $q->where('starts_at', '<', $endsAt)
                    ->where(function (Builder $inner) use ($startsAt) {
                        $inner->whereNull('ends_at')
                            ->orWhere('ends_at', '>', $startsAt);
                    });
            })
            ->exists();
    }

    public function occupancyStats(): array
    {
        $total = ParkingSlot::query()->where('is_disabled', false)->count();
        $occupied = ParkingSlot::query()->where('status', 'occupied')->count();
        $reserved = ParkingSlot::query()->where('status', 'reserved')->count();
        $free = ParkingSlot::query()->where('status', 'free')->where('is_disabled', false)->count();

        return compact('total', 'occupied', 'reserved', 'free');
    }
}
