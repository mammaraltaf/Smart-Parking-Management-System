@extends('layouts.app')
@section('heading', 'Bookings')
@section('sidebar')@include('partials.admin-sidebar')@endsection
@section('content')
@foreach($bookings as $b)
    <div class="mb-2 flex flex-wrap items-center justify-between rounded-lg border border-slate-800 bg-slate-900 px-4 py-3 text-sm">
        <span>{{ $b->user->name }} · {{ $b->parkingSlot->slot_id }} · {{ $b->starts_at->format('M j H:i') }}</span>
        <span class="rounded bg-slate-800 px-2 py-0.5">{{ $b->status }}</span>
        @if(in_array($b->status, ['pending','confirmed','active']))
            <form method="POST" action="{{ route('admin.bookings.cancel', $b) }}">@csrf<button class="text-red-400">Cancel</button></form>
        @endif
    </div>
@endforeach
{{ $bookings->links() }}
@endsection
