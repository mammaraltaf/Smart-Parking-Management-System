<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Smart Parking') — {{ config('app.name', 'Smart Parking') }}</title>
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="flex min-h-screen">
        @auth
            <aside class="hidden w-64 shrink-0 border-r border-slate-800 bg-slate-900/80 p-6 lg:block">
                <a href="{{ route('dashboard') }}" class="mb-8 block text-lg font-semibold text-emerald-400">Smart Parking</a>
                <nav class="space-y-1 text-sm">
                    @yield('sidebar')
                </nav>
                <form method="POST" action="{{ route('logout') }}" class="mt-10">
                    @csrf
                    <button type="submit" class="w-full rounded-lg border border-slate-700 px-3 py-2 text-left text-slate-300 hover:bg-slate-800">Sign out</button>
                </form>
            </aside>
        @endauth

        <div class="flex flex-1 flex-col">
            <header class="border-b border-slate-800 bg-slate-900/50 px-4 py-4 backdrop-blur lg:px-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-semibold">@yield('heading')</h1>
                        @hasSection('subheading')
                            <p class="text-sm text-slate-400">@yield('subheading')</p>
                        @endif
                    </div>
                    @auth
                        <span class="rounded-full bg-slate-800 px-3 py-1 text-xs text-slate-300">{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->role) }}</span>
                    @endauth
                </div>
            </header>

            <main class="flex-1 p-4 lg:p-8">
                @if (session('success'))
                    <div class="mb-4 rounded-lg border border-emerald-800 bg-emerald-950/50 px-4 py-3 text-emerald-300">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 rounded-lg border border-red-800 bg-red-950/50 px-4 py-3 text-red-300">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-red-800 bg-red-950/50 px-4 py-3 text-red-300">
                        <ul class="list-inside list-disc text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
