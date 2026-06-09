<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Services\ParkingAvailabilityService;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(ParkingAvailabilityService $parking): View
    {
        $stats = $parking->occupancyStats();
        $revenue = Payment::query()->where('status', 'completed')->sum('amount');
        $activeBookings = Booking::query()->whereIn('status', ['pending', 'confirmed', 'active'])->count();
        $users = User::query()->count();

        return view('admin.dashboard', compact('stats', 'revenue', 'activeBookings', 'users'));
    }
}
