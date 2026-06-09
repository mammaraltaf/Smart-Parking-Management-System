@extends('layouts.app')
@section('heading', 'Parking slots')
@section('sidebar')@include('partials.admin-sidebar')@endsection
@section('content')
<form method="POST" action="{{ route('admin.slots.store') }}" class="mb-6 grid gap-3 rounded-xl border border-slate-800 bg-slate-900 p-4 md:grid-cols-6">
    @csrf
    <input name="slot_id" placeholder="Slot ID" required class="rounded border border-slate-700 bg-slate-800 px-2 py-2 text-sm">
    <select name="zone_id" required class="rounded border border-slate-700 bg-slate-800 px-2 py-2 text-sm">@foreach($zones as $z)<option value="{{ $z->id }}">{{ $z->name }}</option>@endforeach</select>
    <select name="size" class="rounded border border-slate-700 bg-slate-800 px-2 py-2 text-sm"><option value="standard">Standard</option><option value="compact">Compact</option><option value="large">Large</option></select>
    <select name="type" class="rounded border border-slate-700 bg-slate-800 px-2 py-2 text-sm"><option value="general">General</option><option value="vip">VIP</option><option value="disabled">Disabled</option><option value="ev">EV</option></select>
    <input name="hourly_rate" type="number" step="0.01" placeholder="Rate/hr" class="rounded border border-slate-700 bg-slate-800 px-2 py-2 text-sm">
    <button class="rounded-lg bg-emerald-600 py-2 text-sm">Add slot</button>
</form>
@foreach($slots as $slot)
    <form method="POST" action="{{ route('admin.slots.update', $slot) }}" class="mb-2 flex flex-wrap items-center gap-2 rounded-lg border border-slate-800 bg-slate-900 px-4 py-3 text-sm">
        @csrf @method('PUT')
        <input type="hidden" name="size" value="{{ $slot->size }}">
        <span class="w-20 font-mono text-emerald-400">{{ $slot->slot_id }}</span>
        <select name="zone_id" class="rounded border border-slate-700 bg-slate-800 px-2 py-1">@foreach($zones as $z)<option value="{{ $z->id }}" @selected($slot->zone_id==$z->id)>{{ $z->code }}</option>@endforeach</select>
        <select name="status" class="rounded border border-slate-700 bg-slate-800 px-2 py-1"><option value="free" @selected($slot->status=='free')>Free</option><option value="occupied" @selected($slot->status=='occupied')>Occupied</option><option value="reserved" @selected($slot->status=='reserved')>Reserved</option></select>
        <select name="type" class="rounded border border-slate-700 bg-slate-800 px-2 py-1"><option value="general" @selected($slot->type=='general')>General</option><option value="vip" @selected($slot->type=='vip')>VIP</option><option value="disabled" @selected($slot->type=='disabled')>Disabled</option><option value="ev" @selected($slot->type=='ev')>EV</option></select>
        <input name="hourly_rate" type="number" step="0.01" value="{{ $slot->hourly_rate }}" class="w-24 rounded border border-slate-700 bg-slate-800 px-2 py-1">
        <label class="flex items-center gap-1"><input type="checkbox" name="is_disabled" value="1" @checked($slot->is_disabled)> Disabled</label>
        <button class="text-emerald-400">Save</button>
    </form>
@endforeach
{{ $slots->links() }}
@endsection
