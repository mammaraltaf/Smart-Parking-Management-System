@extends('layouts.app')
@section('heading', 'Profile')
@section('sidebar')@include('partials.driver-sidebar')@endsection
@section('content')
<form method="POST" action="{{ route('driver.profile.update') }}" class="max-w-md space-y-4 rounded-xl border border-slate-800 bg-slate-900 p-6">
    @csrf @method('PUT')
    <div><label class="text-sm text-slate-400">Name</label><input name="name" value="{{ old('name', $user->name) }}" class="mt-1 w-full rounded border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <div><label class="text-sm text-slate-400">Phone</label><input name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 w-full rounded border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <div><label class="text-sm text-slate-400">New password (optional)</label><input type="password" name="password" class="mt-1 w-full rounded border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <div><label class="text-sm text-slate-400">Confirm password</label><input type="password" name="password_confirmation" class="mt-1 w-full rounded border border-slate-700 bg-slate-800 px-3 py-2"></div>
    <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium">Save</button>
</form>
@endsection
