<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EntryExitLog;
use Illuminate\View\View;

class EntryLogController extends Controller
{
    public function index(): View
    {
        $logs = EntryExitLog::query()
            ->with(['vehicle', 'booking.user'])
            ->latest('occurred_at')
            ->paginate(30);

        return view('admin.logs.index', compact('logs'));
    }
}
