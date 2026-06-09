@extends('layouts.guest')
@section('title', 'Register')
@section('content')
<h2 class="mb-6 text-xl font-semibold">Create driver account</h2>
<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf
    <div><label class="mb-1 block text-sm text-slate-400">Full name</label><input name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <div><label class="mb-1 block text-sm text-slate-400">Email</label><input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <div><label class="mb-1 block text-sm text-slate-400">Phone</label><input name="phone" value="{{ old('phone') }}" required class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <div><label class="mb-1 block text-sm text-slate-400">Password</label><input type="password" name="password" required class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <div><label class="mb-1 block text-sm text-slate-400">Confirm password</label><input type="password" name="password_confirmation" required class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <hr class="border-slate-700">
    <div><label class="mb-1 block text-sm text-slate-400">License plate</label><input name="license_plate" value="{{ old('license_plate') }}" required class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 uppercase"></div>
    <div class="grid grid-cols-2 gap-3">
        <div><label class="mb-1 block text-sm text-slate-400">Make</label><input name="vehicle_make" value="{{ old('vehicle_make') }}" class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2"></div>
        <div><label class="mb-1 block text-sm text-slate-400">Model</label><input name="vehicle_model" value="{{ old('vehicle_model') }}" class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2"></div>
    </div>
    <button type="submit" class="w-full rounded-lg bg-emerald-600 py-2.5 font-medium hover:bg-emerald-500">Register</button>
</form>
@endsection
@section('footer')<a href="{{ route('login') }}" class="text-emerald-400">Already have an account?</a>@endsection
