<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class LprCameraController extends Controller
{
    public function show(): View
    {
        return view('guard.lpr.camera', [
            'lprUrl' => config('lpr.service_url'),
        ]);
    }

    /**
     * Proxy image to Python LPR service (avoids CORS; keeps browser from calling Python directly).
     */
    public function recognize(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:5120'], // KB
        ]);

        $url = rtrim(config('lpr.service_url'), '/').'/recognize';

        try {
            $response = Http::timeout(120)
                ->attach(
                    'image',
                    file_get_contents($request->file('image')->getRealPath()),
                    'capture.jpg',
                    ['Content-Type' => $request->file('image')->getMimeType()]
                )
                ->post($url);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'LPR service unreachable. Start it with: cd lpr-service && python -m uvicorn main:app --host 127.0.0.1 --port 8787',
                'detail' => config('app.debug') ? $e->getMessage() : null,
            ], 503);
        }

        if (! $response->successful()) {
            return response()->json([
                'success' => false,
                'error' => 'LPR service error',
                'detail' => $response->body(),
            ], 502);
        }

        return response()->json($response->json());
    }
}
