<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Parking Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100">
    <header class="border-b border-slate-800 px-6 py-4">
        <div class="mx-auto flex max-w-6xl items-center justify-between">
            <span class="text-xl font-bold text-emerald-400">Smart Parking</span>
            <div class="flex gap-3">
                <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm text-slate-300 hover:text-white">Sign in</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">Register</a>
            </div>
        </div>
    </header>
    <main class="mx-auto max-w-6xl px-6 py-16">
        <h1 class="text-4xl font-bold tracking-tight">Automated parking for modern facilities</h1>
        <p class="mt-4 max-w-2xl text-lg text-slate-400">Reserve slots online, verify entry with QR or license plate, and manage occupancy in real time.</p>
        <div class="mt-10 grid gap-4 sm:grid-cols-4">
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-6"><p class="text-3xl font-bold text-emerald-400">{{ $stats['free'] }}</p><p class="text-sm text-slate-400">Free slots</p></div>
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-6"><p class="text-3xl font-bold text-red-400">{{ $stats['occupied'] }}</p><p class="text-sm text-slate-400">Occupied</p></div>
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-6"><p class="text-3xl font-bold text-amber-400">{{ $stats['reserved'] }}</p><p class="text-sm text-slate-400">Reserved</p></div>
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-6"><p class="text-3xl font-bold">{{ $stats['total'] }}</p><p class="text-sm text-slate-400">Total slots</p></div>
        </div>
    </main>
</body>
</html>
