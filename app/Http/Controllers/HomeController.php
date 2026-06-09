<?php

namespace App\Http\Controllers;

use App\Services\ParkingAvailabilityService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(ParkingAvailabilityService $parking): View
    {
        $stats = $parking->occupancyStats();

        return view('home', compact('stats'));
    }
}
