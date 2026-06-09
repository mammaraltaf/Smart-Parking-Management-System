@extends('layouts.app')
@section('heading', 'My vehicles')
@section('sidebar')@include('partials.driver-sidebar')@endsection
@section('content')
<form method="POST" action="{{ route('driver.vehicles.store') }}" class="mb-6 grid gap-3 rounded-xl border border-slate-800 bg-slate-900 p-5 md:grid-cols-4">
    @csrf
    <input name="license_plate" placeholder="License plate" required class="rounded border border-slate-700 bg-slate-800 px-3 py-2 uppercase">
    <input name="make" placeholder="Make" class="rounded border border-slate-700 bg-slate-800 px-3 py-2">
    <input name="model" placeholder="Model" class="rounded border border-slate-700 bg-slate-800 px-3 py-2">
    <button class="rounded-lg bg-emerald-600 py-2 text-sm font-medium">Add vehicle</button>
</form>
<ul class="divide-y divide-slate-800 rounded-xl border border-slate-800">
    @foreach($vehicles as $v)
        <li class="flex items-center justify-between px-5 py-3">
            <span class="font-mono font-medium">{{ $v->license_plate }}</span>
            <span class="text-sm text-slate-400">{{ trim(($v->make ?? '').' '.($v->model ?? '')) }}</span>
            <form method="POST" action="{{ route('driver.vehicles.destroy', $v) }}">@csrf @method('DELETE')<button class="text-sm text-red-400">Remove</button></form>
        </li>
    @endforeach
</ul>
@endsection
