@extends('layouts.app')
@section('heading', 'Security dashboard')
@section('sidebar')@include('partials.guard-sidebar')@endsection
@section('content')
<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <h2 class="mb-3 font-medium">Live slot status</h2>
        <div class="grid grid-cols-4 gap-2 sm:grid-cols-6">
            @foreach($slots as $slot)
                <div title="{{ $slot->slot_id }}" class="rounded-lg border p-2 text-center text-xs {{ $slot->status === 'free' ? 'border-emerald-800 bg-emerald-950/40 text-emerald-400' : ($slot->status === 'occupied' ? 'border-red-800 bg-red-950/40 text-red-400' : 'border-amber-800 bg-amber-950/40 text-amber-400') }}">{{ $slot->slot_id }}</div>
            @endforeach
        </div>
    </div>
    <div>
        <h2 class="mb-3 font-medium">Recent activity</h2>
        <ul class="divide-y divide-slate-800 rounded-xl border border-slate-800 text-sm">
            @foreach($recentLogs as $log)
                <li class="px-4 py-2">{{ $log->occurred_at->format('H:i') }} — {{ $log->direction }} — {{ $log->license_plate_guess ?? '—' }} — <span class="{{ $log->result === 'allowed' ? 'text-emerald-400' : 'text-red-400' }}">{{ $log->result }}</span></li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
