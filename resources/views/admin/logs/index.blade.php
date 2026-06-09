@extends('layouts.app')
@section('heading', 'Entry / exit logs')
@section('sidebar')@include('partials.admin-sidebar')@endsection
@section('content')
<table class="w-full text-sm rounded-xl border border-slate-800">
    <thead class="bg-slate-900 text-slate-400 text-left"><tr><th class="px-4 py-3">Time</th><th class="px-4 py-3">Gate</th><th class="px-4 py-3">Direction</th><th class="px-4 py-3">Plate</th><th class="px-4 py-3">Method</th><th class="px-4 py-3">Result</th></tr></thead>
    <tbody class="divide-y divide-slate-800">
    @foreach($logs as $log)
        <tr><td class="px-4 py-3">{{ $log->occurred_at->format('Y-m-d H:i:s') }}</td><td class="px-4 py-3">{{ $log->gate_id }}</td><td class="px-4 py-3">{{ $log->direction }}</td><td class="px-4 py-3 font-mono">{{ $log->license_plate_guess ?? $log->vehicle?->license_plate ?? '—' }}</td><td class="px-4 py-3">{{ $log->verification_method }}</td><td class="px-4 py-3 @if($log->result==='allowed') text-emerald-400 @else text-red-400 @endif">{{ $log->result }}</td></tr>
    @endforeach
    </tbody>
</table>
{{ $logs->links() }}
@endsection
