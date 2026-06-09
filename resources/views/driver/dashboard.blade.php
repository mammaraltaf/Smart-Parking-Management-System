@extends('layouts.app')
@section('title', 'Driver')
@section('heading', 'Driver dashboard')
@section('sidebar')@include('partials.driver-sidebar')@endsection
@section('content')
<div class="mb-8 grid gap-4 sm:grid-cols-4">
    <div class="rounded-xl border border-slate-800 bg-slate-900 p-5"><p class="text-2xl font-bold text-emerald-400">{{ $stats['free'] }}</p><p class="text-sm text-slate-400">Free</p></div>
    <div class="rounded-xl border border-slate-800 bg-slate-900 p-5"><p class="text-2xl font-bold text-red-400">{{ $stats['occupied'] }}</p><p class="text-sm text-slate-400">Occupied</p></div>
    <div class="rounded-xl border border-slate-800 bg-slate-900 p-5"><p class="text-2xl font-bold text-amber-400">{{ $stats['reserved'] }}</p><p class="text-sm text-slate-400">Reserved</p></div>
    <div class="rounded-xl border border-slate-800 bg-slate-900 p-5"><p class="text-2xl font-bold">{{ $stats['total'] }}</p><p class="text-sm text-slate-400">Total slots</p></div>
</div>
<div class="flex flex-wrap gap-3">
    <a href="{{ route('driver.bookings.search') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium">Book parking</a>
    <a href="{{ route('driver.bookings.index') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm">My bookings</a>
</div>
<div class="mt-8 rounded-xl border border-slate-800 bg-slate-900">
    <div class="border-b border-slate-800 px-5 py-3 font-medium">Recent bookings</div>
    <div class="divide-y divide-slate-800">
        @forelse($bookings as $b)
            <a href="{{ route('driver.bookings.show', $b) }}" class="flex justify-between px-5 py-3 hover:bg-slate-800/50">
                <span>{{ $b->parkingSlot->slot_id }} · {{ $b->parkingSlot->zone->name }}</span>
                <span class="text-sm text-slate-400">{{ ucfirst($b->status) }}</span>
            </a>
        @empty
            <p class="px-5 py-6 text-slate-500">No bookings yet.</p>
        @endforelse
    </div>
</div>
@endsection
