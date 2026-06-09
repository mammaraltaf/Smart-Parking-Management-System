@extends('layouts.app')
@section('heading', 'Activity logs')
@section('sidebar')@include('partials.guard-sidebar')@endsection
@section('content')
<table class="w-full text-sm rounded-xl border border-slate-800">
    <thead class="bg-slate-900 text-slate-400 text-left"><tr><th class="px-4 py-3">Time</th><th class="px-4 py-3">Gate</th><th class="px-4 py-3">Direction</th><th class="px-4 py-3">Plate</th><th class="px-4 py-3">Result</th></tr></thead>
    <tbody class="divide-y divide-slate-800">
    @foreach($logs as $log)
        <tr><td class="px-4 py-3">{{ $log->occurred_at->format('Y-m-d H:i:s') }}</td><td class="px-4 py-3">{{ $log->gate_id }}</td><td class="px-4 py-3">{{ $log->direction }}</td><td class="px-4 py-3 font-mono">{{ $log->license_plate_guess ?? '—' }}</td><td class="px-4 py-3">{{ $log->result }}</td></tr>
    @endforeach
    </tbody>
</table>
{{ $logs->links() }}
@endsection
