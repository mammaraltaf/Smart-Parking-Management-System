@extends('layouts.app')
@section('title', 'Camera LPR')
@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('heading', 'Webcam license plate (YOLO + OCR)')
@section('subheading', 'Point the laptop camera at a plate — capture runs YOLOv8 + EasyOCR on the Python service.')

@section('sidebar')
    @include('partials.guard-sidebar')
@endsection

@section('content')
<div class="grid gap-6 lg:grid-cols-2">
    <div class="rounded-xl border border-slate-800 bg-slate-900 p-4">
        <video id="cam" autoplay playsinline muted class="w-full rounded-lg bg-black object-cover" style="max-height:420px;"></video>
        <div class="mt-3 flex flex-wrap gap-3">
            <button type="button" id="btnStart" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium">Start camera</button>
            <button type="button" id="btnSnap" disabled class="rounded-lg border border-slate-600 px-4 py-2 text-sm">Capture & read plate</button>
            <button type="button" id="btnStop" disabled class="rounded-lg border border-red-900 px-4 py-2 text-sm text-red-400">Stop</button>
        </div>
        <label class="mt-3 flex items-center gap-2 text-sm text-slate-400">
            <input type="checkbox" id="autoScan"> Auto every 3s while camera runs
        </label>
        <p class="mt-2 text-xs text-amber-500/90">Ensure the Python LPR service is running on <span class="font-mono">{{ $lprUrl }}</span> — see <code class="rounded bg-slate-800 px-1">lpr-service/README.md</code>.</p>
    </div>

    <div class="space-y-4">
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-4">
            <h3 class="mb-2 text-sm font-medium text-slate-300">Recognition result</h3>
            <div id="result" class="min-h-[4rem] font-mono text-lg text-emerald-400"></div>
            <pre id="raw" class="mt-3 max-h-48 overflow-auto text-xs text-slate-500"></pre>
        </div>

        <form method="POST" action="{{ route('guard.verify.plate') }}" class="rounded-xl border border-slate-800 bg-slate-900 p-4 space-y-3">
            @csrf
            <input type="hidden" name="gate_id" value="CAM-01">
            <select name="direction" class="w-full rounded border border-slate-700 bg-slate-800 px-3 py-2 text-sm">
                <option value="entry">Entry</option>
                <option value="exit">Exit</option>
            </select>
            <div>
                <label class="text-xs text-slate-400">License plate (filled from capture)</label>
                <input name="license_plate" id="plateField" class="mt-1 w-full rounded border border-slate-700 bg-slate-800 px-3 py-2 font-mono uppercase" placeholder="Run capture first…">
            </div>
            <button class="w-full rounded-lg bg-emerald-600 py-2 text-sm font-medium">Submit to gate log</button>
        </form>
    </div>
</div>

<script>
(function () {
    const recognizeUrl = @json(route('guard.lpr.recognize'));

    const video = document.getElementById('cam');
    const btnStart = document.getElementById('btnStart');
    const btnSnap = document.getElementById('btnSnap');
    const btnStop = document.getElementById('btnStop');
    const autoScan = document.getElementById('autoScan');
    const resultEl = document.getElementById('result');
    const rawEl = document.getElementById('raw');
    const plateField = document.getElementById('plateField');

    let stream = null;
    let autoTimer = null;

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    async function startCam() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false,
            });
            video.srcObject = stream;
            btnSnap.disabled = false;
            btnStop.disabled = false;
            btnStart.disabled = true;
            if (autoScan.checked) startAuto();
        } catch (e) {
            resultEl.innerHTML = '<span class="text-red-400">Camera permission denied or unavailable.</span>';
            rawEl.textContent = String(e);
        }
    }

    function stopCam() {
        if (autoTimer) {
            clearInterval(autoTimer);
            autoTimer = null;
        }
        if (stream) {
            stream.getTracks().forEach((t) => t.stop());
            stream = null;
        }
        video.srcObject = null;
        btnSnap.disabled = true;
        btnStop.disabled = true;
        btnStart.disabled = false;
    }

    async function capture() {
        const vw = Math.max(video.videoWidth, 1);
        const vh = Math.max(video.videoHeight, 1);
        const canvas = document.createElement('canvas');
        canvas.width = vw;
        canvas.height = vh;
        const ctx = canvas.getContext('2d');
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, vw, vh);

        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.85));
        if (!blob) {
            resultEl.textContent = 'Could not encode frame.';
            return;
        }

        const fd = new FormData();
        fd.append('image', blob, 'frame.jpg');

        resultEl.textContent = 'Analyzing…';
        rawEl.textContent = '';

        const res = await fetch(recognizeUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: fd,
            credentials: 'same-origin',
        });

        const txt = await res.text();
        let data = {};
        try {
            data = JSON.parse(txt);
        } catch {
            resultEl.innerHTML = '<span class="text-red-400">Unexpected response</span>';
            rawEl.textContent = txt.slice(0, 2000);
            return;
        }
        rawEl.textContent = JSON.stringify(data, null, 2);

        if (!res.ok || data.success === false || (data.primary_plate === undefined && data.error)) {
            resultEl.innerHTML = '<span class="text-red-400">' + (data.error || data.detail || 'Request failed') + '</span>';
            return;
        }

        resultEl.textContent = data.primary_plate || '(no confident read)';
        if (data.primary_plate) {
            plateField.value = data.primary_plate;
        }
    }

    function startAuto() {
        if (autoTimer) clearInterval(autoTimer);
        autoTimer = setInterval(() => {
            if (!btnSnap.disabled) capture();
        }, 3000);
    }

    btnStart.addEventListener('click', startCam);
    btnStop.addEventListener('click', stopCam);
    btnSnap.addEventListener('click', capture);
    autoScan.addEventListener('change', () => {
        if (autoScan.checked && stream) startAuto();
        else if (autoTimer) {
            clearInterval(autoTimer);
            autoTimer = null;
        }
    });
})();
</script>
@endsection
