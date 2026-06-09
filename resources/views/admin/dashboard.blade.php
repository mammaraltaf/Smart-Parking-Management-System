@extends('layouts.app')
@section('heading', 'Admin dashboard')
@section('sidebar')@include('partials.admin-sidebar')@endsection
@section('content')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-xl border border-slate-800 bg-slate-900 p-5"><p class="text-2xl font-bold text-emerald-400">{{ $stats['free'] }}</p><p class="text-sm text-slate-400">Free slots</p></div>
    <div class="rounded-xl border border-slate-800 bg-slate-900 p-5"><p class="text-2xl font-bold text-red-400">{{ $stats['occupied'] }}</p><p class="text-sm text-slate-400">Occupied</p></div>
    <div class="rounded-xl border border-slate-800 bg-slate-900 p-5"><p class="text-2xl font-bold">PKR {{ number_format($revenue, 0) }}</p><p class="text-sm text-slate-400">Total revenue</p></div>
    <div class="rounded-xl border border-slate-800 bg-slate-900 p-5"><p class="text-2xl font-bold">{{ $activeBookings }}</p><p class="text-sm text-slate-400">Active bookings</p></div>
</div>
<p class="mt-4 text-sm text-slate-400">{{ $users }} registered users</p>
@endsection
