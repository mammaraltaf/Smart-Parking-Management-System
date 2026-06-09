<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use App\Models\EntryExitLog;
use App\Models\ParkingSlot;
use Illuminate\View\View;

class GuardDashboardController extends Controller
{
    public function __invoke(): View
    {
        $slots = ParkingSlot::query()->with('zone')->orderBy('slot_id')->get();
        $recentLogs = EntryExitLog::query()->with('vehicle')->latest('occurred_at')->limit(10)->get();

        return view('guard.dashboard', compact('slots', 'recentLogs'));
    }
}
