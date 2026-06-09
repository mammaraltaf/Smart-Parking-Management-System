<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Auth::user()->payments()->with('booking.parkingSlot')->latest()->paginate(10);

        return view('driver.payments.index', compact('payments'));
    }

    public function checkout(Booking $booking, BillingService $billing): View
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $breakdown = $billing->calculateForBooking($booking);

        return view('driver.payments.checkout', compact('booking', 'breakdown'));
    }

    public function pay(Request $request, Booking $booking, BillingService $billing): RedirectResponse
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $data = $request->validate(['method' => ['required', 'in:cash,card,online']]);
        $breakdown = $billing->calculateForBooking($booking);

        $payment = $billing->recordPayment(
            $booking,
            $data['method'],
            $breakdown['total'],
            $breakdown['late_fee'],
        );

        return redirect()->route('driver.payments.invoice', $payment)->with('success', 'Payment recorded.');
    }

    public function invoice(Payment $payment): View
    {
        abort_unless($payment->user_id === Auth::id(), 403);
        $payment->load(['booking.parkingSlot.zone', 'booking.vehicle', 'user']);

        return view('driver.payments.invoice', compact('payment'));
    }
}
