<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'description'])]
class Zone extends Model
{
    public function parkingSlots(): HasMany
    {
        return $this->hasMany(ParkingSlot::class);
    }
}
