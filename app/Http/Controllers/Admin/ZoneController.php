<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZoneController extends Controller
{
    public function index(): View
    {
        $zones = Zone::query()->withCount('parkingSlots')->orderBy('name')->get();

        return view('admin.zones.index', compact('zones'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:32', 'unique:zones,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Zone::query()->create($data);

        return back()->with('success', 'Zone created.');
    }

    public function update(Request $request, Zone $zone): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:32', 'unique:zones,code,'.$zone->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $zone->update($data);

        return back()->with('success', 'Zone updated.');
    }

    public function destroy(Zone $zone): RedirectResponse
    {
        if ($zone->parkingSlots()->exists()) {
            return back()->with('error', 'Remove slots from this zone first.');
        }

        $zone->delete();

        return back()->with('success', 'Zone deleted.');
    }
}
