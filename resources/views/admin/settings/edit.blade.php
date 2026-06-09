@extends('layouts.app')
@section('heading', 'System settings')
@section('sidebar')@include('partials.admin-sidebar')@endsection
@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-md space-y-4 rounded-xl border border-slate-800 bg-slate-900 p-6">
    @csrf @method('PUT')
    <div><label class="text-sm text-slate-400">Default hourly rate (PKR)</label><input type="number" step="0.01" name="hourly_rate" value="{{ $hourly_rate }}" class="mt-1 w-full rounded border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <div><label class="text-sm text-slate-400">Late fee per hour (PKR)</label><input type="number" step="0.01" name="late_fee_per_hour" value="{{ $late_fee_per_hour }}" class="mt-1 w-full rounded border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <div><label class="text-sm text-slate-400">Booking hold (minutes)</label><input type="number" name="booking_hold_minutes" value="{{ $booking_hold_minutes }}" class="mt-1 w-full rounded border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium">Save settings</button>
</form>
@endsection
