@extends('layouts.app')
@section('heading', 'Zones')
@section('sidebar')@include('partials.admin-sidebar')@endsection
@section('content')
<form method="POST" action="{{ route('admin.zones.store') }}" class="mb-6 flex flex-wrap gap-3 rounded-xl border border-slate-800 bg-slate-900 p-4">
    @csrf
    <input name="code" placeholder="Code (A1)" required class="rounded border border-slate-700 bg-slate-800 px-3 py-2 text-sm">
    <input name="name" placeholder="Zone name" required class="rounded border border-slate-700 bg-slate-800 px-3 py-2 text-sm">
    <input name="description" placeholder="Description" class="flex-1 rounded border border-slate-700 bg-slate-800 px-3 py-2 text-sm">
    <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm">Add zone</button>
</form>
@foreach($zones as $zone)
    <div class="mb-3 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-800 bg-slate-900 px-5 py-4">
        <div><span class="font-mono text-emerald-400">{{ $zone->code }}</span> — {{ $zone->name }} <span class="text-sm text-slate-500">({{ $zone->parking_slots_count }} slots)</span></div>
        <form method="POST" action="{{ route('admin.zones.update', $zone) }}" class="flex gap-2">@csrf @method('PUT')
            <input name="code" value="{{ $zone->code }}" class="rounded border border-slate-700 bg-slate-800 px-2 py-1 text-sm">
            <input name="name" value="{{ $zone->name }}" class="rounded border border-slate-700 bg-slate-800 px-2 py-1 text-sm">
            <button class="text-sm text-emerald-400">Save</button>
        </form>
        <form method="POST" action="{{ route('admin.zones.destroy', $zone) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-sm text-red-400">Delete</button></form>
    </div>
@endforeach
@endsection
