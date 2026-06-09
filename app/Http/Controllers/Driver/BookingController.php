<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Zone;
use App\Services\BookingService;
use App\Services\ParkingAvailabilityService;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function search(Request $request, ParkingAvailabilityService $parking): View
    {
        $zones = Zone::query()->orderBy('name')->get();
        $startsAt = $request->filled('starts_at')
            ? Carbon::parse($request->string('starts_at'))
            : now()->addHour()->startOfHour();
        $endsAt = $request->filled('ends_at')
            ? Carbon::parse($request->string('ends_at'))
            : $startsAt->copy()->addHours(2);

        $slots = $request->has('search')
            ? $parking->availableSlots(
                $request->integer('zone_id') ?: null,
                $request->string('type')->toString() ?: null,
                $request->string('size')->toString() ?: null,
                $startsAt,
                $endsAt,
            )
            : collect();

        return view('driver.bookings.search', compact('zones', 'slots', 'startsAt', 'endsAt'));
    }

    public function store(Request $request, BookingService $bookings): RedirectResponse
    {
        $data = $request->validate([
            'parking_slot_id' => ['required', 'exists:parking_slots,id'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
        ]);

        $vehicle = Auth::user()->vehicles()->findOrFail($data['vehicle_id']);
        $slot = \App\Models\ParkingSlot::query()->findOrFail($data['parking_slot_id']);

        try {
            $booking = $bookings->create(
                Auth::user(),
                $slot,
                Carbon::parse($data['starts_at']),
                isset($data['ends_at']) ? Carbon::parse($data['ends_at']) : null,
                $vehicle->id,
            );
            $bookings->confirm($booking);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('driver.bookings.show', $booking)->with('success', 'Booking confirmed!');
    }

    public function index(): View
    {
        $bookings = Auth::user()
            ->bookings()
            ->with(['parkingSlot.zone', 'vehicle'])
            ->latest()
            ->paginate(10);

        return view('driver.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking, QrCodeService $qr): View
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $booking->load(['parkingSlot.zone', 'vehicle']);
        $qrSvg = $qr->svg($booking->qr_token);

        return view('driver.bookings.show', compact('booking', 'qrSvg'));
    }

    public function cancel(Booking $booking, BookingService $bookings): RedirectResponse
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        if (! in_array($booking->status, ['pending', 'confirmed'], true)) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $bookings->cancel($booking);

        return redirect()->route('driver.bookings.index')->with('success', 'Booking cancelled.');
    }
}
