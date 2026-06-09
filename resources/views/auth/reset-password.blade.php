@extends('layouts.guest')
@section('title', 'Reset password')
@section('content')
<h2 class="mb-6 text-xl font-semibold">Enter OTP & new password</h2>
<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    <div><label class="mb-1 block text-sm text-slate-400">OTP (6 digits)</label><input name="otp" maxlength="6" required class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <div><label class="mb-1 block text-sm text-slate-400">New password</label><input type="password" name="password" required class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <div><label class="mb-1 block text-sm text-slate-400">Confirm</label><input type="password" name="password_confirmation" required class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <button type="submit" class="w-full rounded-lg bg-emerald-600 py-2.5 font-medium">Update password</button>
</form>
@endsection
