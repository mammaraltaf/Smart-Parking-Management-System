@extends('layouts.app')
@section('heading', 'Find parking')
@section('sidebar')@include('partials.driver-sidebar')@endsection
@section('content')
<form method="GET" class="mb-6 grid gap-4 rounded-xl border border-slate-800 bg-slate-900 p-5 md:grid-cols-5">
    <input type="hidden" name="search" value="1">
    <div><label class="text-xs text-slate-400">From</label><input type="datetime-local" name="starts_at" value="{{ $startsAt->format('Y-m-d\TH:i') }}" class="mt-1 w-full rounded border border-slate-700 bg-slate-800 px-2 py-1.5 text-sm"></div>
    <div><label class="text-xs text-slate-400">To</label><input type="datetime-local" name="ends_at" value="{{ $endsAt->format('Y-m-d\TH:i') }}" class="mt-1 w-full rounded border border-slate-700 bg-slate-800 px-2 py-1.5 text-sm"></div>
    <div><label class="text-xs text-slate-400">Zone</label><select name="zone_id" class="mt-1 w-full rounded border border-slate-700 bg-slate-800 px-2 py-1.5 text-sm"><option value="">All</option>@foreach($zones as $z)<option value="{{ $z->id }}" @selected(request('zone_id')==$z->id)>{{ $z->name }}</option>@endforeach</select></div>
    <div><label class="text-xs text-slate-400">Type</label><select name="type" class="mt-1 w-full rounded border border-slate-700 bg-slate-800 px-2 py-1.5 text-sm"><option value="">Any</option>@foreach(['general','vip','disabled','ev'] as $t)<option value="{{ $t }}" @selected(request('type')==$t)>{{ ucfirst($t) }}</option>@endforeach</select></div>
    <div class="flex items-end"><button class="w-full rounded-lg bg-emerald-600 py-2 text-sm font-medium">Search</button></div>
</form>
@if(request('search'))
<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
    @forelse($slots as $slot)
        <form method="POST" action="{{ route('driver.bookings.store') }}" class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            @csrf
            <input type="hidden" name="parking_slot_id" value="{{ $slot->id }}">
            <input type="hidden" name="starts_at" value="{{ $startsAt->toIso8601String() }}">
            <input type="hidden" name="ends_at" value="{{ $endsAt->toIso8601String() }}">
            <h3 class="font-semibold text-emerald-400">{{ $slot->slot_id }}</h3>
            <p class="text-sm text-slate-400">{{ $slot->zone->name }} · {{ ucfirst($slot->type) }} · {{ ucfirst($slot->size) }}</p>
            <p class="mt-2 text-sm">PKR {{ $slot->hourly_rate ?? \App\Models\Setting::hourlyRate() }}/hr</p>
            <select name="vehicle_id" required class="mt-3 w-full rounded border border-slate-700 bg-slate-800 px-2 py-1 text-sm">
                @foreach(auth()->user()->vehicles as $v)<option value="{{ $v->id }}">{{ $v->license_plate }}</option>@endforeach
            </select>
            <button class="mt-3 w-full rounded-lg bg-emerald-600 py-2 text-sm">Reserve</button>
        </form>
    @empty
        <p class="text-slate-500">No slots available for this period.</p>
    @endforelse
</div>
@endif
@endsection
