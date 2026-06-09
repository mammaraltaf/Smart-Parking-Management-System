<?php

namespace Database\Seeders;

use App\Models\ParkingSlot;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('hourly_rate', config('parking.default_hourly_rate'));
        Setting::set('late_fee_per_hour', config('parking.late_fee_per_hour'));
        Setting::set('booking_hold_minutes', config('parking.booking_hold_minutes'));

        User::query()->updateOrCreate(
            ['email' => 'admin@parking.test'],
            [
                'name' => 'System Admin',
                'phone' => '03000000001',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'guard@parking.test'],
            [
                'name' => 'Security Guard',
                'phone' => '03000000002',
                'password' => Hash::make('password'),
                'role' => User::ROLE_GUARD,
                'is_active' => true,
            ],
        );

        $driver = User::query()->updateOrCreate(
            ['email' => 'driver@parking.test'],
            [
                'name' => 'Demo Driver',
                'phone' => '03000000003',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DRIVER,
                'is_active' => true,
            ],
        );

        Vehicle::query()->updateOrCreate(
            ['license_plate' => 'LEA-1234'],
            ['user_id' => $driver->id, 'make' => 'Toyota', 'model' => 'Corolla', 'color' => 'White'],
        );

        $zoneA = Zone::query()->updateOrCreate(['code' => 'A'], ['name' => 'Zone A — Ground', 'description' => 'Main entrance level']);
        $zoneB = Zone::query()->updateOrCreate(['code' => 'B'], ['name' => 'Zone B — Basement', 'description' => 'Covered parking']);

        foreach (range(1, 12) as $i) {
            ParkingSlot::query()->updateOrCreate(
                ['slot_id' => 'A-'.str_pad((string) $i, 2, '0', STR_PAD_LEFT)],
                [
                    'zone_id' => $zoneA->id,
                    'size' => $i <= 2 ? 'large' : 'standard',
                    'type' => $i === 1 ? 'disabled' : ($i === 2 ? 'vip' : 'general'),
                    'status' => 'free',
                    'hourly_rate' => 50,
                ],
            );
        }

        foreach (range(1, 8) as $i) {
            ParkingSlot::query()->updateOrCreate(
                ['slot_id' => 'B-'.str_pad((string) $i, 2, '0', STR_PAD_LEFT)],
                [
                    'zone_id' => $zoneB->id,
                    'size' => 'standard',
                    'type' => $i === 8 ? 'ev' : 'general',
                    'status' => 'free',
                    'hourly_rate' => 40,
                ],
            );
        }
    }
}
