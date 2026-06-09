@extends('layouts.app')
@section('heading', 'Invoice')
@section('sidebar')@include('partials.driver-sidebar')@endsection
@section('content')
<div id="invoice" class="max-w-lg rounded-xl border border-slate-800 bg-white p-8 text-slate-900 print:shadow-none">
    <h2 class="text-xl font-bold">Parking Invoice</h2>
    <p class="text-sm text-slate-600">Ref: {{ $payment->reference }}</p>
    <hr class="my-4">
    <p><strong>{{ $payment->user->name }}</strong></p>
    <p class="text-sm">Vehicle: {{ $payment->booking?->vehicle?->license_plate ?? 'N/A' }}</p>
    <p class="text-sm">Slot: {{ $payment->booking?->parkingSlot?->slot_id ?? 'N/A' }}</p>
    <p class="mt-4 text-2xl font-bold">PKR {{ number_format($payment->amount, 2) }}</p>
    <p class="text-sm">Method: {{ ucfirst($payment->method) }} · {{ $payment->created_at->format('d M Y H:i') }}</p>
</div>
<button onclick="window.print()" class="mt-4 rounded-lg bg-emerald-600 px-4 py-2 text-sm">Print / Save PDF</button>
@endsection
