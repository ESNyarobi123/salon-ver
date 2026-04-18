<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f0a1e">
    <title>TIPTAP · {{ config('salon.portal_order') }} @if(isset($restaurant)) · {{ $restaurant->name }} @endif</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpeg') }}">
    <link rel="shortcut icon" href="{{ asset('logo.jpeg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; box-sizing: border-box; }
        body { background: #0f0a1e; min-height: 100vh; min-height: 100dvh; -webkit-tap-highlight-color: transparent; padding-left: env(safe-area-inset-left); padding-right: env(safe-area-inset-right); }
        .glass { background: rgba(255, 255, 255, 0.04); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .glass-card { background: rgba(28, 22, 51, 0.65); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.06); }
        .gradient-text { background: linear-gradient(135deg, #a78bfa 0%, #22d3ee 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .card-hover { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -8px rgba(0,0,0,0.4); }
        @media (hover: none) { .card-hover:hover { transform: none; } }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.03); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(139, 92, 246, 0.4); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(139, 92, 246, 0.6); }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .touch-action-manipulation { touch-action: manipulation; }
    </style>
</head>
<body class="font-sans antialiased text-white min-h-screen">
    <div class="fixed inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <div class="absolute top-0 right-0 w-[min(80vw,500px)] h-[min(80vw,500px)] bg-violet-600/15 rounded-full blur-[120px] -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-[min(80vw,500px)] h-[min(80vw,500px)] bg-cyan-600/15 rounded-full blur-[120px] -ml-32 -mb-32"></div>
    </div>

    <header class="sticky top-0 z-30 border-b border-white/10 glass pt-[env(safe-area-inset-top)]">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <div class="flex items-center gap-2 shrink-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full overflow-hidden flex items-center justify-center shadow-lg shadow-violet-500/20">
                        <img src="{{ asset('logo.jpeg') }}" alt="TIPTAP Logo" class="w-full h-full object-cover">
                    </div>
                    <span class="text-lg sm:text-xl font-black text-white tracking-tight">TIPTAP <span class="gradient-text">{{ config('salon.portal_order') }}</span></span>
                </div>
                @if(isset($restaurant))
                    <span class="hidden md:inline text-white/50 text-sm font-medium truncate max-w-[180px] lg:max-w-xs" title="{{ $restaurant->name }}">· {{ $restaurant->name }}</span>
                @endif
            </div>
            <form action="{{ route('order-portal.logout') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-xl text-white/70 hover:text-white hover:bg-white/10 active:bg-white/15 transition-colors text-sm font-semibold touch-action-manipulation">Toka</button>
            </form>
        </div>
    </header>

    <main class="relative z-10 max-w-[1600px] mx-auto px-4 sm:px-6 py-4 sm:py-6 lg:py-8 pb-[max(1.5rem,env(safe-area-inset-bottom))]">
        @if (session('success'))
            <div class="mb-4 sm:mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium" role="alert">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
