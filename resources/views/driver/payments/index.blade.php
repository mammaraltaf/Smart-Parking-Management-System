@extends('layouts.app')
@section('heading', 'Payment history')
@section('sidebar')@include('partials.driver-sidebar')@endsection
@section('content')
<table class="w-full overflow-hidden rounded-xl border border-slate-800 text-sm">
    <thead class="bg-slate-900 text-left text-slate-400"><tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3">Method</th><th class="px-4 py-3"></th></tr></thead>
    <tbody class="divide-y divide-slate-800">
    @foreach($payments as $p)
        <tr><td class="px-4 py-3">{{ $p->created_at->format('M j, Y H:i') }}</td><td class="px-4 py-3">PKR {{ number_format($p->amount, 2) }}</td><td class="px-4 py-3">{{ ucfirst($p->method) }}</td><td class="px-4 py-3"><a href="{{ route('driver.payments.invoice', $p) }}" class="text-emerald-400">Invoice</a></td></tr>
    @endforeach
    </tbody>
</table>
{{ $payments->links() }}
@endsection
