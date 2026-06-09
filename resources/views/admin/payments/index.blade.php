@extends('layouts.app')
@section('heading', 'Revenue & payments')
@section('sidebar')@include('partials.admin-sidebar')@endsection
@section('content')
<p class="mb-4 text-lg">Total (filtered): <strong class="text-emerald-400">PKR {{ number_format($total, 2) }}</strong>
    <a href="{{ route('admin.payments.export') }}" class="ml-4 text-sm text-emerald-400">Export CSV</a></p>
<table class="w-full text-sm rounded-xl border border-slate-800 overflow-hidden">
    <thead class="bg-slate-900 text-slate-400 text-left"><tr><th class="px-4 py-3">User</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3">Method</th><th class="px-4 py-3">Date</th></tr></thead>
    <tbody class="divide-y divide-slate-800">@foreach($payments as $p)<tr><td class="px-4 py-3">{{ $p->user->name }}</td><td class="px-4 py-3">PKR {{ number_format($p->amount,2) }}</td><td class="px-4 py-3">{{ $p->method }}</td><td class="px-4 py-3">{{ $p->created_at->format('Y-m-d H:i') }}</td></tr>@endforeach</tbody>
</table>
{{ $payments->links() }}
@endsection
