@extends('layouts.app')
@section('heading', 'Users')
@section('sidebar')@include('partials.admin-sidebar')@endsection
@section('content')
<form method="GET" class="mb-4 flex gap-2"><input name="search" value="{{ request('search') }}" placeholder="Search" class="rounded border border-slate-700 bg-slate-800 px-3 py-2 text-sm"><select name="role" class="rounded border border-slate-700 bg-slate-800 px-3 py-2 text-sm"><option value="">All roles</option><option value="driver">Driver</option><option value="admin">Admin</option><option value="guard">Guard</option></select><button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm">Filter</button></form>
@foreach($users as $user)
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mb-2 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-800 bg-slate-900 px-4 py-3 text-sm">
        @csrf @method('PUT')
        <div><strong>{{ $user->name }}</strong> — {{ $user->email }} · {{ $user->phone }}</div>
        <select name="role" class="rounded border border-slate-700 bg-slate-800 px-2 py-1"><option value="driver" @selected($user->role=='driver')>Driver</option><option value="admin" @selected($user->role=='admin')>Admin</option><option value="guard" @selected($user->role=='guard')>Guard</option></select>
        <label><input type="checkbox" name="is_active" value="1" @checked($user->is_active)> Active</label>
        <button class="text-emerald-400">Update</button>
    </form>
@endforeach
{{ $users->links() }}
@endsection
