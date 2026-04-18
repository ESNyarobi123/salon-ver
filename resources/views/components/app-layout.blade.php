<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TIPTAP') }} - Manager</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
        
        <!-- Favicon -->
        <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpeg') }}">
        <link rel="shortcut icon" href="{{ asset('logo.jpeg') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/lucide@latest"></script>
        <style>
            [x-cloak] { display: none !important; }
            .sidebar-collapsed { width: 80px !important; }
            .main-expanded { margin-left: 80px !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#F8FAFC] text-deep-blue">
        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-deep-blue text-white transition-all duration-300 ease-in-out overflow-y-auto overflow-x-hidden shadow-2xl">
                <div class="flex flex-col h-full p-6">
                    <!-- Logo -->
                    <div class="flex items-center justify-between mb-10 px-2">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center overflow-hidden rounded-full">
                                <img src="{{ asset('logo.jpeg') }}" alt="TIPTAP Logo" class="w-full h-full object-cover">
                            </div>
                            <span class="sidebar-text font-black text-2xl tracking-tighter whitespace-nowrap transition-opacity duration-300">TAP<span class="text-orange-red">TAP</span></span>
                        </div>
                        <button id="toggle-sidebar" class="p-2 hover:bg-white/10 rounded-xl transition-colors">
                            <i data-lucide="chevron-left" id="toggle-icon" class="w-5 h-5 text-gray-400"></i>
                        </button>
                    </div>

                    <!-- Nav Links -->
                    <nav class="flex-1 space-y-1">
                        <p class="sidebar-text text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-4 px-4 opacity-50">Main Menu</p>
                        
                        <x-nav-link href="{{ route('manager.dashboard') }}" :active="request()->routeIs('manager.dashboard')" icon="layout-dashboard" label="Dashboard" />
                        <x-nav-link href="{{ route('manager.orders.live') }}" :active="request()->routeIs('manager.orders.live')" icon="shopping-bag" label="Live Orders" badge="12" />
                        <x-nav-link href="{{ route('manager.menu.index') }}" :active="request()->routeIs('manager.menu.index')" icon="utensils-crossed" label="Menu Management" />
                        <x-nav-link href="{{ route('manager.waiters.index') }}" :active="request()->routeIs('manager.waiters.index')" icon="users" label="{{ config('salon.staff_plural') }} &amp; team" />

                        <p class="sidebar-text text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mt-8 mb-4 px-4 opacity-50">Finance & Feedback</p>
                        
                        <x-nav-link href="{{ route('manager.payments.index') }}" :active="request()->routeIs('manager.payments.index')" icon="credit-card" label="Payments" />
                        <x-nav-link href="{{ route('manager.feedback.index') }}" :active="request()->routeIs('manager.feedback.index')" icon="message-square" label="Feedback" />
                        <x-nav-link href="{{ route('manager.tips.index') }}" :active="request()->routeIs('manager.tips.index')" icon="coins" label="Tips Tracking" />

                        <p class="sidebar-text text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mt-8 mb-4 px-4 opacity-50">System</p>
                        <x-nav-link href="{{ route('manager.api.index') }}" :active="request()->routeIs('manager.api.index')" icon="qr-code" label="QR & Mobile API" />
                    </nav>

                    <!-- Bottom Section -->
                    <div class="mt-auto pt-6 border-t border-white/5">
                        <div class="flex items-center gap-4 mb-6 px-2 overflow-hidden">
                            <div class="flex-shrink-0 w-10 h-10 bg-yellow-orange rounded-xl flex items-center justify-center font-black text-deep-blue shadow-lg shadow-yellow-orange/20">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="sidebar-text transition-opacity duration-300 whitespace-nowrap">
                                <p class="text-sm font-black">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] font-bold text-orange-red uppercase tracking-widest">Manager</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-4 px-4 py-3 rounded-xl text-gray-400 hover:text-orange-red hover:bg-orange-red/5 font-bold transition-all group">
                                <i data-lucide="log-out" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                                <span class="sidebar-text transition-opacity duration-300">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div id="main-wrapper" class="flex-1 ml-72 transition-all duration-300 ease-in-out min-h-screen flex flex-col">
                <!-- Header -->
                <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-40 px-8 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button id="mobile-menu" class="lg:hidden p-2 hover:bg-gray-50 rounded-xl">
                            <i data-lucide="menu" class="w-6 h-6"></i>
                        </button>
                        <h2 class="text-xl font-black tracking-tight text-deep-blue">
                            {{ $header ?? 'Dashboard' }}
                        </h2>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex items-center gap-2 bg-green-50 px-3 py-1.5 rounded-lg border border-green-100">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                            <span class="text-[10px] font-black text-green-600 uppercase tracking-widest">System Live</span>
                        </div>
                        <button class="p-2.5 bg-gray-50 text-gray-400 hover:text-deep-blue hover:bg-gray-100 rounded-xl transition-all relative">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-orange-red rounded-full border-2 border-white"></span>
                        </button>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-8 flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                lucide.createIcons();

                const sidebar = document.getElementById('sidebar');
                const mainWrapper = document.getElementById('main-wrapper');
                const toggleBtn = document.getElementById('toggle-sidebar');
                const toggleIcon = document.getElementById('toggle-icon');
                const sidebarTexts = document.querySelectorAll('.sidebar-text');

                toggleBtn.addEventListener('click', () => {
                    const isCollapsed = sidebar.classList.contains('w-72');
                    
                    if (isCollapsed) {
                        // Collapse
                        sidebar.classList.replace('w-72', 'w-[80px]');
                        mainWrapper.classList.replace('ml-72', 'ml-[80px]');
                        sidebarTexts.forEach(t => t.classList.add('opacity-0', 'pointer-events-none'));
                        toggleIcon.style.transform = 'rotate(180deg)';
                    } else {
                        // Expand
                        sidebar.classList.replace('w-[80px]', 'w-72');
                        mainWrapper.classList.replace('ml-[80px]', 'ml-72');
                        sidebarTexts.forEach(t => t.classList.remove('opacity-0', 'pointer-events-none'));
                        toggleIcon.style.transform = 'rotate(0deg)';
                    }
                });
            });
        </script>
    </body>
</html>
