<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::query()->with(['user', 'booking'])->where('status', 'completed');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        $payments = $query->latest()->paginate(25);
        $total = (clone $query)->sum('amount');

        return view('admin.payments.index', compact('payments', 'total'));
    }

    public function export(Request $request): StreamedResponse
    {
        $payments = Payment::query()->with(['user', 'booking'])->where('status', 'completed')->latest()->get();

        return response()->streamDownload(function () use ($payments) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'User', 'Amount', 'Method', 'Reference', 'Date']);
            foreach ($payments as $p) {
                fputcsv($out, [
                    $p->id,
                    $p->user?->name,
                    $p->amount,
                    $p->method,
                    $p->reference,
                    $p->created_at?->toDateTimeString(),
                ]);
            }
            fclose($out);
        }, 'payments-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }
}
