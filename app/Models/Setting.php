<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'key';

    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $row = static::query()->find($key);

            return $row?->value ?? $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => (string) $value]);
        Cache::forget("setting.{$key}");
    }

    public static function hourlyRate(): float
    {
        return (float) static::get('hourly_rate', config('parking.default_hourly_rate'));
    }

    public static function lateFeePerHour(): float
    {
        return (float) static::get('late_fee_per_hour', config('parking.late_fee_per_hour'));
    }

    public static function bookingHoldMinutes(): int
    {
        return (int) static::get('booking_hold_minutes', config('parking.booking_hold_minutes'));
    }
}
