<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use App\Services\EntryExitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function index(): View
    {
        return view('guard.verify.index');
    }

    public function qr(Request $request, EntryExitService $entryExit): RedirectResponse
    {
        $data = $request->validate([
            'qr_token' => ['required', 'string'],
            'gate_id' => ['required', 'string', 'max:64'],
            'direction' => ['required', 'in:entry,exit'],
        ]);

        $log = $entryExit->verifyQr($data['qr_token'], $data['gate_id'], $data['direction']);

        return back()->with(
            $log->result === 'allowed' ? 'success' : 'error',
            $log->result === 'allowed'
                ? ucfirst($data['direction']).' allowed for '.($log->license_plate_guess ?? 'vehicle').'.'
                : ($log->meta['reason'] ?? 'Access denied.')
        );
    }

    public function plate(Request $request, EntryExitService $entryExit): RedirectResponse
    {
        $data = $request->validate([
            'license_plate' => ['required', 'string', 'max:32'],
            'gate_id' => ['required', 'string', 'max:64'],
            'direction' => ['required', 'in:entry,exit'],
        ]);

        $log = $entryExit->verifyPlate($data['license_plate'], $data['gate_id'], $data['direction']);

        return back()->with(
            $log->result === 'allowed' ? 'success' : 'error',
            $log->result === 'allowed'
                ? ucfirst($data['direction']).' allowed.'
                : ($log->meta['reason'] ?? 'Access denied.')
        );
    }
}
