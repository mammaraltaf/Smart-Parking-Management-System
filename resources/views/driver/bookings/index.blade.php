@extends('layouts.app')
@section('heading', 'My bookings')
@section('sidebar')@include('partials.driver-sidebar')@endsection
@section('content')
<div class="overflow-hidden rounded-xl border border-slate-800">
    <table class="w-full text-sm">
        <thead class="bg-slate-900 text-left text-slate-400"><tr><th class="px-4 py-3">Slot</th><th class="px-4 py-3">When</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr></thead>
        <tbody class="divide-y divide-slate-800">
        @foreach($bookings as $b)
            <tr class="hover:bg-slate-900/50">
                <td class="px-4 py-3">{{ $b->parkingSlot->slot_id }} ({{ $b->parkingSlot->zone->name }})</td>
                <td class="px-4 py-3">{{ $b->starts_at->format('M j, H:i') }} @if($b->ends_at)– {{ $b->ends_at->format('H:i') }}@endif</td>
                <td class="px-4 py-3"><span class="rounded bg-slate-800 px-2 py-0.5">{{ ucfirst($b->status) }}</span></td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('driver.bookings.show', $b) }}" class="text-emerald-400">QR</a>
                    @if(in_array($b->status, ['confirmed','active','completed']))
                        <a href="{{ route('driver.payments.checkout', $b) }}" class="ml-2 text-slate-300">Pay</a>
                    @endif
                    @if(in_array($b->status, ['pending','confirmed']))
                        <form method="POST" action="{{ route('driver.bookings.cancel', $b) }}" class="inline">@csrf<button class="ml-2 text-red-400">Cancel</button></form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{{ $bookings->links() }}
@endsection
