<?php

return [
    'default_hourly_rate' => (float) env('PARKING_HOURLY_RATE', 50),
    'late_fee_per_hour' => (float) env('PARKING_LATE_FEE_PER_HOUR', 25),
    'booking_hold_minutes' => (int) env('PARKING_BOOKING_HOLD_MINUTES', 15),
    'grace_minutes' => (int) env('PARKING_GRACE_MINUTES', 10),
    'max_login_attempts' => (int) env('PARKING_MAX_LOGIN_ATTEMPTS', 5),
    'lockout_minutes' => (int) env('PARKING_LOCKOUT_MINUTES', 15),
];
