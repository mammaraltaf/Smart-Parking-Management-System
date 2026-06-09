<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slot_id',
    'zone_id',
    'size',
    'type',
    'status',
    'is_reserved_slot',
    'is_disabled',
    'hourly_rate',
])]
class ParkingSlot extends Model
{
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
