<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class ExpirePendingBookings extends Command
{
    protected $signature = 'bookings:expire-pending';

    protected $description = 'Expire unpaid or unconfirmed bookings past their hold window';

    public function handle(BookingService $bookings): int
    {
        $count = $bookings->expirePending();
        $this->info("Expired {$count} booking(s).");

        return self::SUCCESS;
    }
}
