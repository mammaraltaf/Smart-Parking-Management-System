<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->with(['user', 'parkingSlot.zone', 'vehicle'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function cancel(Booking $booking, BookingService $bookings): RedirectResponse
    {
        if (in_array($booking->status, ['pending', 'confirmed', 'active'], true)) {
            $bookings->cancel($booking);
        }

        return back()->with('success', 'Booking cancelled by admin.');
    }
}
