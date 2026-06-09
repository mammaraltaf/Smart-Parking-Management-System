@extends('layouts.app')
@section('heading', 'Verify entry / exit')
@section('sidebar')@include('partials.guard-sidebar')@endsection
@section('content')
<div class="grid gap-6 lg:grid-cols-2">
    <form method="POST" action="{{ route('guard.verify.qr') }}" class="rounded-xl border border-slate-800 bg-slate-900 p-6 space-y-4">
        @csrf
        <h2 class="font-medium">QR / booking token</h2>
        <input name="qr_token" required placeholder="Paste QR token" class="w-full rounded border border-slate-700 bg-slate-800 px-3 py-2 font-mono text-sm">
        <input name="gate_id" value="GATE-01" required class="w-full rounded border border-slate-700 bg-slate-800 px-3 py-2 text-sm">
        <select name="direction" class="w-full rounded border border-slate-700 bg-slate-800 px-3 py-2 text-sm"><option value="entry">Entry</option><option value="exit">Exit</option></select>
        <button class="w-full rounded-lg bg-emerald-600 py-2 text-sm font-medium">Verify QR</button>
    </form>
    <form method="POST" action="{{ route('guard.verify.plate') }}" class="rounded-xl border border-slate-800 bg-slate-900 p-6 space-y-4">
        @csrf
        <h2 class="font-medium">License plate (LPR / manual)</h2>
        <input name="license_plate" required placeholder="ABC-123" class="w-full rounded border border-slate-700 bg-slate-800 px-3 py-2 uppercase font-mono">
        <input name="gate_id" value="GATE-01" required class="w-full rounded border border-slate-700 bg-slate-800 px-3 py-2 text-sm">
        <select name="direction" class="w-full rounded border border-slate-700 bg-slate-800 px-3 py-2 text-sm"><option value="entry">Entry</option><option value="exit">Exit</option></select>
        <button class="w-full rounded-lg bg-emerald-600 py-2 text-sm font-medium">Verify plate</button>
    </form>
</div>
@endsection
