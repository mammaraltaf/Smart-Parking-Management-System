@extends('layouts.app')
@section('heading', 'Pay parking fee')
@section('sidebar')@include('partials.driver-sidebar')@endsection
@section('content')
<div class="max-w-md rounded-xl border border-slate-800 bg-slate-900 p-6">
    <p class="text-slate-400">Slot {{ $booking->parkingSlot->slot_id }}</p>
    <p class="mt-2 text-3xl font-bold">PKR {{ number_format($breakdown['total'], 2) }}</p>
    <ul class="mt-4 space-y-1 text-sm text-slate-400">
        <li>Duration: {{ $breakdown['hours'] }} hr(s) @ PKR {{ $breakdown['hourly_rate'] }}/hr</li>
        <li>Base: PKR {{ number_format($breakdown['base_amount'], 2) }}</li>
        @if($breakdown['late_fee'] > 0)<li class="text-amber-400">Late fee: PKR {{ number_format($breakdown['late_fee'], 2) }}</li>@endif
    </ul>
    <form method="POST" action="{{ route('driver.payments.pay', $booking) }}" class="mt-6 space-y-3">
        @csrf
        <select name="method" class="w-full rounded border border-slate-700 bg-slate-800 px-3 py-2"><option value="cash">Cash</option><option value="card">Card</option><option value="online">Online</option></select>
        <button class="w-full rounded-lg bg-emerald-600 py-2.5 font-medium">Confirm payment</button>
    </form>
</div>
@endsection
