@extends('layouts.app')
@section('heading', 'Booking '.$booking->public_id)
@section('sidebar')@include('partials.driver-sidebar')@endsection
@section('content')
<div class="grid gap-6 lg:grid-cols-2">
    <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
        <p class="text-sm text-slate-400">Reservation ID</p>
        <p class="font-mono text-lg">{{ $booking->public_id }}</p>
        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-slate-400">Slot</dt><dd>{{ $booking->parkingSlot->slot_id }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Zone</dt><dd>{{ $booking->parkingSlot->zone->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Vehicle</dt><dd>{{ $booking->vehicle?->license_plate ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Status</dt><dd>{{ ucfirst($booking->status) }}</dd></div>
        </dl>
        <p class="mt-4 text-xs text-slate-500">Show this QR at the gate for entry verification.</p>
    </div>
    <div class="flex flex-col items-center rounded-xl border border-slate-800 bg-white p-6 text-slate-900">
        <div class="w-48">{!! $qrSvg !!}</div>
        <p class="mt-2 break-all text-center text-xs font-mono">{{ $booking->qr_token }}</p>
    </div>
</div>
@endsection
