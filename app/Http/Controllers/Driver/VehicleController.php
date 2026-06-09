<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(): View
    {
        $vehicles = Auth::user()->vehicles()->latest()->get();

        return view('driver.vehicles.index', compact('vehicles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'license_plate' => ['required', 'string', 'max:32', 'unique:vehicles,license_plate'],
            'make' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
        ]);

        Auth::user()->vehicles()->create([
            'license_plate' => strtoupper($data['license_plate']),
            'make' => $data['make'] ?? null,
            'model' => $data['model'] ?? null,
            'color' => $data['color'] ?? null,
        ]);

        return back()->with('success', 'Vehicle added.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        abort_unless($vehicle->user_id === Auth::id(), 403);
        $vehicle->delete();

        return back()->with('success', 'Vehicle removed.');
    }
}
