<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParkingSlot;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParkingSlotController extends Controller
{
    public function index(): View
    {
        $slots = ParkingSlot::query()->with('zone')->orderBy('slot_id')->paginate(20);
        $zones = Zone::query()->orderBy('name')->get();

        return view('admin.slots.index', compact('slots', 'zones'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'slot_id' => ['required', 'string', 'max:64', 'unique:parking_slots,slot_id'],
            'zone_id' => ['required', 'exists:zones,id'],
            'size' => ['required', 'in:compact,standard,large'],
            'type' => ['required', 'in:general,vip,disabled,ev'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'is_disabled' => ['boolean'],
        ]);

        ParkingSlot::query()->create([
            ...$data,
            'status' => 'free',
            'is_disabled' => $request->boolean('is_disabled'),
        ]);

        return back()->with('success', 'Slot created.');
    }

    public function update(Request $request, ParkingSlot $slot): RedirectResponse
    {
        $data = $request->validate([
            'zone_id' => ['required', 'exists:zones,id'],
            'size' => ['required', 'in:compact,standard,large'],
            'type' => ['required', 'in:general,vip,disabled,ev'],
            'status' => ['required', 'in:free,occupied,reserved'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'is_disabled' => ['boolean'],
        ]);

        if ($slot->status === 'occupied' && $data['status'] !== 'occupied') {
            $active = $slot->bookings()->whereIn('status', ['active'])->exists();
            if ($active) {
                return back()->with('error', 'Cannot change status while a vehicle is inside.');
            }
        }

        $slot->update([
            ...$data,
            'is_disabled' => $request->boolean('is_disabled'),
        ]);

        return back()->with('success', 'Slot updated.');
    }

    public function destroy(ParkingSlot $slot): RedirectResponse
    {
        if ($slot->bookings()->whereIn('status', ['pending', 'confirmed', 'active'])->exists()) {
            return back()->with('error', 'Cannot delete slot with active bookings.');
        }

        $slot->delete();

        return back()->with('success', 'Slot deleted.');
    }
}
