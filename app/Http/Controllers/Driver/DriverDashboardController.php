<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Services\ParkingAvailabilityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DriverDashboardController extends Controller
{
    public function __invoke(ParkingAvailabilityService $parking): View
    {
        $user = Auth::user();
        $stats = $parking->occupancyStats();
        $bookings = $user->bookings()->with('parkingSlot.zone')->latest()->limit(5)->get();

        return view('driver.dashboard', compact('stats', 'bookings'));
    }
}
