@extends('layouts.guest')
@section('title', 'Sign in')
@section('content')
<h2 class="mb-6 text-xl font-semibold">Sign in</h2>
<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf
    <div>
        <label class="mb-1 block text-sm text-slate-400">Email or phone</label>
        <input name="login" value="{{ old('login') }}" required class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2">
    </div>
    <div>
        <label class="mb-1 block text-sm text-slate-400">Password</label>
        <input type="password" name="password" required class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2">
    </div>
    <label class="flex items-center gap-2 text-sm text-slate-400"><input type="checkbox" name="remember"> Remember me</label>
    <button type="submit" class="w-full rounded-lg bg-emerald-600 py-2.5 font-medium hover:bg-emerald-500">Sign in</button>
</form>
@endsection
@section('footer')
    <a href="{{ route('register') }}" class="text-emerald-400">Create account</a> ·
    <a href="{{ route('password.request') }}" class="text-emerald-400">Forgot password?</a>
@endsection
