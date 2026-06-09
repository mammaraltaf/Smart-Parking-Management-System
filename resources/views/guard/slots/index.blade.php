@extends('layouts.app')
@section('heading', 'Slot monitor')
@section('sidebar')@include('partials.guard-sidebar')@endsection
@section('content')
<div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">
    @foreach($slots as $slot)
        <div class="rounded-xl border p-4 text-center {{ $slot->status === 'free' ? 'border-emerald-700 bg-emerald-950/30' : ($slot->status === 'occupied' ? 'border-red-700 bg-red-950/30' : 'border-amber-700 bg-amber-950/30') }}">
            <p class="font-mono font-semibold">{{ $slot->slot_id }}</p>
            <p class="text-xs text-slate-400">{{ $slot->zone->code }}</p>
            <p class="mt-1 text-xs capitalize">{{ $slot->status }}</p>
        </div>
    @endforeach
</div>
@endsection
