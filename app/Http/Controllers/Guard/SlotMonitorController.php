<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use App\Models\ParkingSlot;
use Illuminate\View\View;

class SlotMonitorController extends Controller
{
    public function index(): View
    {
        $slots = ParkingSlot::query()->with('zone')->orderBy('zone_id')->orderBy('slot_id')->get();

        return view('guard.slots.index', compact('slots'));
    }
}
