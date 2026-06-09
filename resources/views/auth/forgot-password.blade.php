@extends('layouts.guest')
@section('title', 'Forgot password')
@section('content')
<h2 class="mb-6 text-xl font-semibold">Reset password</h2>
<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
    @csrf
    <div>
        <label class="mb-1 block text-sm text-slate-400">Email or phone</label>
        <input name="login" required class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2">
    </div>
    <button type="submit" class="w-full rounded-lg bg-emerald-600 py-2.5 font-medium">Send OTP</button>
</form>
@endsection
@section('footer')<a href="{{ route('login') }}" class="text-emerald-400">Back to sign in</a>@endsection
