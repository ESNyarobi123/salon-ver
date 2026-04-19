<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TIPTAP Manager</title>
    
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpeg') }}">
    <link rel="shortcut icon" href="{{ asset('logo.jpeg') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        :root {
            --font-family-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --font-size-page-title: clamp(28px, 2vw + 1rem, 32px);
            --font-size-section-heading: clamp(18px, 1vw + 0.75rem, 20px);
            --font-size-body: 15px;
            --font-size-badge: 11px;
        }

        * {
            font-family: var(--font-family-sans);
        }

        body,
        button,
        input,
        select,
        textarea,
        label {
            font-family: var(--font-family-sans);
            font-weight: 400;
            font-size: var(--font-size-body);
        }

        h1,
        .heading-page {
            font-size: var(--font-size-page-title);
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        h2,
        h3,
        .section-heading {
            font-size: var(--font-size-section-heading);
            font-weight: 600;
        }

        label,
        button,
        .btn,
        .action-link {
            font-weight: 500;
        }

        .badge,
        .status-pill,
        .tag-pill,
        .uppercase-pill {
            font-weight: 500;
            font-size: var(--font-size-badge);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        body { 
            background: #0f0a1e;
            min-height: 100vh;
        }

        /* Premium Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(139, 92, 246, 0.5) 0%, rgba(6, 182, 212, 0.5) 100%);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, rgba(139, 92, 246, 0.8) 0%, rgba(6, 182, 212, 0.8) 100%);
        }

        /* Glassmorphism Effects */
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .glass-card {
            background: rgba(28, 22, 51, 0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .bg-surface-900 {
            background: #0f0a1e;
        }

        /* Sidebar Styling */
        .sidebar-gradient {
            background: #0f0a1e;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .sidebar-link {
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-link:hover {
            background: linear-gradient(90deg, rgba(139, 92, 246, 0.1) 0%, transparent 100%);
            color: #fff;
        }
        
        .sidebar-link-active {
            background: linear-gradient(90deg, rgba(139, 92, 246, 0.2) 0%, transparent 100%);
            color: #fff !important;
        }
        
        .sidebar-link-active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: linear-gradient(180deg, #8b5cf6 0%, #06b6d4 100%);
            border-radius: 0 4px 4px 0;
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #8b5cf6 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(139, 92, 246, 0.3); }
            50% { box-shadow: 0 0 40px rgba(139, 92, 246, 0.5); }
        }

        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }

        /* Card Hover Effects */
        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
            background: rgba(35, 28, 64, 0.8);
        }

        /* Hide scrollbar utility */
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Custom scrollbar for sidebar - hidden but functional */
        .custom-scrollbar::-webkit-scrollbar { display: none; }
        .custom-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .sidebar-link { min-height: 44px; }
        .sidebar-link:focus { outline: none; box-shadow: 0 0 0 2px #0f0a1e, 0 0 0 4px rgba(139, 92, 246, 0.6); }
        /* Sidebar visibility: NOT relying on Tailwind – layout CSS only */
        #mobile-sidebar { transition: transform 0.3s ease-out, width 0.3s ease-out; }
        /* Mobile: closed by default */
        #mobile-sidebar.sidebar-closed-mobile { transform: translateX(-100%) !important; }
        /* Mobile: open when user clicks menu */
        #mobile-sidebar.sidebar-open { transform: translateX(0) !important; visibility: visible !important; }
        /* Desktop (768px+): always visible */
        @media (min-width: 768px) {
            #mobile-sidebar,
            #mobile-sidebar.sidebar-closed-mobile { transform: translateX(0) !important; visibility: visible !important; }
            #mobile-sidebar.sidebar-collapsed { width: 3.5rem !important; }
            main#main-content { margin-left: 12rem; }
            body.sidebar-collapsed-main main#main-content { margin-left: 3.5rem; }
        }
        body.sidebar-mobile-open #sidebar-overlay { display: block !important; opacity: 1 !important; pointer-events: auto !important; }

        /* Sidebar collapsed (desktop): narrow, icons only */
        #mobile-sidebar.sidebar-collapsed { width: 3.5rem; }
        #mobile-sidebar.sidebar-collapsed .sidebar-link span,
        #mobile-sidebar.sidebar-collapsed .sidebar-label,
        #mobile-sidebar.sidebar-collapsed .sidebar-logo-text,
        #mobile-sidebar.sidebar-collapsed .sidebar-user-text { display: none !important; }
        #mobile-sidebar.sidebar-collapsed .sidebar-link { justify-content: center; padding-left: 0; padding-right: 0; margin-left: 0.5rem; margin-right: 0.5rem; }
        #mobile-sidebar.sidebar-collapsed .sidebar-link > div:first-child { margin: 0; }
        #mobile-sidebar.sidebar-collapsed nav .px-6 { padding-left: 0; padding-right: 0; }
        #mobile-sidebar.sidebar-collapsed .sidebar-user-area .flex-1 { display: none; }
        #mobile-sidebar.sidebar-collapsed .sidebar-user-area { justify-content: center; }
        body.sidebar-collapsed-main main#main-content { margin-left: 3.5rem; }
    </style>
</head>
<body class="font-sans antialiased text-white min-h-screen pt-[env(safe-area-inset-top)] pl-[env(safe-area-inset-left)] pr-[env(safe-area-inset-right)] pb-[env(safe-area-inset-bottom)]">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-2 focus:bg-violet-600 focus:text-white focus:rounded-xl focus:ring-2 focus:ring-violet-400 focus:ring-offset-2 focus:ring-offset-[#0f0a1e] focus:outline-none">Skip to main content</a>
    
    <!-- Overlay (mobile only) -->
    <div id="sidebar-overlay" onclick="closeSidebar()" class="fixed inset-0 bg-black/60 z-40 backdrop-blur-sm hidden md:hidden transition-opacity duration-300 opacity-0 cursor-pointer" aria-hidden="true"></div>

    <div class="flex min-h-screen">
        <!-- Premium Manager Sidebar: drawer on mobile, persistent on md+ with toggle -->
        <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 z-[100] w-72 md:w-48 bg-surface-900/95 backdrop-blur-xl border-r border-white/10 z-50 flex flex-col overflow-hidden sidebar-closed-mobile" role="navigation" aria-label="Manager navigation">
            <!-- Logo Area -->
            <div class="p-6 pb-4 flex justify-between items-center border-b border-white/5 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 flex shrink-0 items-center justify-center overflow-hidden rounded-full">
                        <img src="{{ asset('logo.jpeg') }}" alt="TIPTAP Logo" class="w-full h-full object-cover">
                    </div>
                    <div class="sidebar-logo-text min-w-0">
                        <span class="text-xl font-black text-white tracking-tight block leading-none hidden">TIP<span class="gradient-text">TAP</span></span>
                        <span class="text-[10px] font-semibold text-white/40 uppercase tracking-[0.2em]">{{ config('salon.manager_portal_subtitle') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button" id="sidebar-toggle" class="hidden md:flex min-h-[44px] min-w-[44px] items-center justify-center p-2.5 text-white/40 hover:text-white hover:bg-white/10 rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-[#0f0a1e]" aria-label="Collapse sidebar" title="Collapse sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="sidebar-toggle-icon-collapse" title="Collapse"><path d="m15 18-6-6 6-6"/></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="sidebar-toggle-icon-expand" class="hidden" title="Expand"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                    <button type="button" onclick="closeSidebar()" class="md:hidden min-h-[44px] min-w-[44px] inline-flex items-center justify-center p-2.5 text-white/40 hover:text-white hover:bg-white/10 rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-[#0f0a1e]" aria-label="Close menu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 py-4 custom-scrollbar overflow-y-auto overflow-x-hidden">
                <div class="mb-3 px-5 sidebar-label">
                    <p class="text-[9px] font-bold text-white/25 uppercase tracking-[0.25em]">{{ config('salon.manager_sidebar_section_main') }}</p>
                </div>

                <a href="{{ route('manager.dashboard') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.dashboard') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-violet-500/20 to-cyan-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.dashboard') ? 'text-violet-400' : 'text-white/50' }}">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.manager_nav_dashboard') }}</span>
                </a>

                <a href="{{ route('manager.orders.live') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.orders.live') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-amber-500/20 to-orange-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.orders.live') ? 'text-amber-400' : 'text-white/50' }}">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.live_bookings') }}</span>
                </a>

                <a href="{{ route('manager.product-sales.index') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.product-sales.index') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-lime-500/20 to-emerald-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.product-sales.index') ? 'text-lime-400' : 'text-white/50' }}">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.manager_nav_product_sales') }}</span>
                </a>

                <a href="{{ route('manager.orders.history') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.orders.history') || request()->routeIs('manager.orders.show') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-cyan-500/20 to-blue-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.orders.history') || request()->routeIs('manager.orders.show') ? 'text-cyan-400' : 'text-white/50' }}">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.booking_history') }}</span>
                </a>

                <a href="{{ route('manager.menu.index') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.menu.index') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.menu.index') ? 'text-emerald-400' : 'text-white/50' }}">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.services') }}</span>
                </a>

                <a href="{{ route('manager.menu-image.index') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.menu-image.index') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-rose-500/20 to-orange-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.menu-image.index') ? 'text-rose-400' : 'text-white/50' }}">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.service_menu_image') }}</span>
                </a>

                <a href="{{ route('manager.stock.index') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.stock.index', 'manager.stock.update') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-lime-500/20 to-green-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.stock.index', 'manager.stock.update') ? 'text-lime-400' : 'text-white/50' }}">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" x2="12" y1="22.08" y2="12"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs flex-1 min-w-0">{{ config('salon.manager_nav_stock') }}</span>
                    @if(($managerLowStockCount ?? 0) > 0)
                        <span class="shrink-0 min-w-[1.25rem] h-5 px-1.5 flex items-center justify-center rounded-full bg-amber-500/90 text-[10px] font-bold text-surface-900">{{ $managerLowStockCount > 99 ? '99+' : $managerLowStockCount }}</span>
                    @endif
                </a>

                <a href="{{ route('manager.waiters.index') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.waiters.index') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-blue-500/20 to-indigo-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.waiters.index') ? 'text-blue-400' : 'text-white/50' }}">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.staff_plural') }}{!! config('salon.manager_nav_staff_team_suffix') !!}</span>
                </a>

                <a href="{{ route('manager.waiters.history') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.waiters.history') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-indigo-500/20 to-violet-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.waiters.history') ? 'text-indigo-400' : 'text-white/50' }}">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.manager_nav_staff_history') }}</span>
                </a>

                <a href="{{ route('manager.tables.index') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.tables.index') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-purple-500/20 to-pink-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.tables.index') ? 'text-purple-400' : 'text-white/50' }}">
                            <rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.seat_plural') }}{!! config('salon.manager_nav_seat_qr_suffix') !!}</span>
                </a>

                <div class="mt-5 mb-3 px-5 sidebar-label">
                    <p class="text-[9px] font-bold text-white/25 uppercase tracking-[0.25em]">{{ config('salon.manager_sidebar_section_finance') }}</p>
                </div>

                <a href="{{ route('manager.payroll.index') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.payroll.index') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-amber-500/20 to-yellow-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.payroll.index') ? 'text-amber-400' : 'text-white/50' }}">
                            <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.manager_nav_payroll') }}</span>
                </a>

                <a href="{{ route('manager.payroll.history') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.payroll.history') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-slate-500/20 to-zinc-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.payroll.history') ? 'text-slate-300' : 'text-white/50' }}">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.manager_nav_payroll_history') }}</span>
                </a>

                <a href="{{ route('manager.payments.index') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.payments.index') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-pink-500/20 to-rose-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.payments.index') ? 'text-pink-400' : 'text-white/50' }}">
                            <rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.manager_nav_payments') }}</span>
                </a>

                <div class="mt-5 mb-3 px-5 sidebar-label">
                    <p class="text-[9px] font-bold text-white/25 uppercase tracking-[0.25em]">{{ config('salon.manager_sidebar_section_insights') }}</p>
                </div>

                <a href="{{ route('manager.feedback.index') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.feedback.index') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-cyan-500/20 to-teal-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.feedback.index') ? 'text-cyan-400' : 'text-white/50' }}">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.manager_nav_customer_feedback') }}</span>
                </a>

                <a href="{{ route('manager.reports.performance') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.reports.performance') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-orange-500/20 to-amber-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.reports.performance') ? 'text-orange-400' : 'text-white/50' }}">
                            <path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.manager_nav_reports') }}</span>
                </a>

                <a href="{{ route('manager.api.index') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.api.index') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-violet-500/20 to-purple-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.api.index') ? 'text-violet-400' : 'text-white/50' }}">
                            <rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.manager_nav_api') }}</span>
                </a>

                <a href="{{ route('manager.help.index') }}" onclick="closeSidebar()" class="sidebar-link flex items-center gap-2.5 px-4 py-2 mx-2 rounded-lg {{ request()->routeIs('manager.help.index') ? 'sidebar-link-active' : 'text-white/55' }}">
                    <div class="w-7 h-7 rounded-md bg-gradient-to-br from-sky-500/20 to-blue-500/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ request()->routeIs('manager.help.index') ? 'text-sky-400' : 'text-white/50' }}">
                            <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/>
                        </svg>
                    </div>
                    <span class="font-medium text-xs">{{ config('salon.manager_nav_help') }}</span>
                </a>
            </nav>

            <!-- User Profile Area -->
            <div class="p-4 border-t border-white/5 shrink-0 sidebar-user-area">
                <div class="glass-card rounded-xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-violet-600 to-cyan-500 rounded-lg flex items-center justify-center font-bold text-white shadow-lg shadow-violet-500/20">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0 sidebar-user-text">
                        <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] font-medium text-white/40 truncate">{{ config('salon.manager_account_role') }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-white/40 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main id="main-content" class="flex-1 min-h-screen flex flex-col w-full relative z-0 transition-[margin] duration-300 md:ml-72" tabindex="-1">
            <!-- Mobile Header -->
            <div class="md:hidden glass sticky top-0 z-30 px-4 py-3 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="openSidebar()" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center gap-2 px-3 py-2.5 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all active:scale-95 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-[#0f0a1e]" aria-label="Open menu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
                        </svg>
                    </button>
                    <span class="font-bold text-white/90 text-lg tracking-tight">TIP<span class="gradient-text">TAP</span></span>
                </div>
                <div class="w-9 h-9 flex items-center justify-center overflow-hidden rounded-full">
                    <img src="{{ asset('logo.jpeg') }}" alt="TIPTAP Logo" class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Desktop Header & Content -->
            <div class="p-4 md:p-8 flex-1">
                <!-- Desktop Top Bar: sidebar toggle + title -->
                <div class="hidden md:flex justify-between items-center mb-8">
                    <div class="flex items-center gap-5">
                        <button type="button" id="sidebar-toggle-top" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center p-2.5 glass rounded-xl hover:bg-white/10 transition-all focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-[#0f0a1e]" aria-label="Toggle sidebar" title="Toggle sidebar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="sidebar-toggle-top-icon"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/></svg>
                        </button>
                        <div>
                            <p class="text-[11px] font-semibold text-violet-400 uppercase tracking-[0.15em] mb-1">{{ config('salon.manager_portal_subtitle') }}</p>
                            <h1 class="text-3xl font-bold text-white tracking-tight">{{ $header ?? config('salon.manager_nav_dashboard') }}</h1>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-5">
                        <div class="glass px-4 py-2.5 rounded-xl flex items-center gap-3">
                            <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                            <span class="text-[11px] font-semibold text-white/80 uppercase tracking-wider">{{ config('salon.manager_system_live_badge') }}</span>
                        </div>
                    </div>
                </div>

                {{ $slot }}
            </div>

            <!-- Toast Notifications -->
            @if(session('success'))
                <div id="toast-success" class="fixed bottom-8 right-8 z-[200] animate-float">
                    <div class="glass-card px-6 py-4 rounded-2xl border-emerald-500/20 flex items-center gap-4 shadow-2xl shadow-emerald-500/10">
                        <div class="w-10 h-10 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white uppercase tracking-wider">Success</p>
                            <p class="text-sm text-white/60">{{ session('success') }}</p>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white/20 hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>
                </div>
                <script>setTimeout(() => document.getElementById('toast-success')?.remove(), 5000);</script>
            @endif

            @if(session('error'))
                <div id="toast-error" class="fixed bottom-8 right-8 z-[200] animate-float">
                    <div class="glass-card px-6 py-4 rounded-2xl border-rose-500/20 flex items-center gap-4 shadow-2xl shadow-rose-500/10">
                        <div class="w-10 h-10 bg-rose-500/20 rounded-xl flex items-center justify-center text-rose-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white uppercase tracking-wider">Error</p>
                            <p class="text-sm text-white/60">{{ session('error') }}</p>
                        </div>
                        <button type="button" onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white/20 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white/30 rounded" aria-label="Dismiss">×</button>
                    </div>
                </div>
                <script>setTimeout(() => document.getElementById('toast-error')?.remove(), 5000);</script>
            @endif
            @if(session('info'))
                <div id="toast-info" class="fixed bottom-8 right-8 z-[200] animate-float">
                    <div class="glass-card px-6 py-4 rounded-2xl border-cyan-500/20 flex items-center gap-4 shadow-2xl shadow-cyan-500/10">
                        <div class="w-10 h-10 bg-cyan-500/20 rounded-xl flex items-center justify-center text-cyan-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white uppercase tracking-wider">Info</p>
                            <p class="text-sm text-white/60">{{ session('info') }}</p>
                        </div>
                        <button type="button" onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white/20 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white/30 rounded" aria-label="Dismiss">×</button>
                    </div>
                </div>
                <script>setTimeout(() => document.getElementById('toast-info')?.remove(), 5000);</script>
            @endif
        </main>
    </div>

    <script>
        function openSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (!sidebar || !overlay) return;
            sidebar.classList.remove('sidebar-closed-mobile');
            sidebar.classList.add('sidebar-open');
            document.body.classList.add('sidebar-mobile-open');
            overlay.classList.remove('hidden');
            overlay.classList.remove('opacity-0');
            overlay.classList.add('opacity-100');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (!sidebar || !overlay) return;
            sidebar.classList.remove('sidebar-open');
            sidebar.classList.add('sidebar-closed-mobile');
            document.body.classList.remove('sidebar-mobile-open');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            setTimeout(function() { overlay.classList.add('hidden'); }, 300);
            document.body.style.overflow = '';
        }
        function isSidebarVisible() {
            var el = document.getElementById('mobile-sidebar');
            if (!el) return false;
            var r = el.getBoundingClientRect();
            return r.left >= -10 && r.width > 0;
        }
        function toggleSidebar() {
            if (document.getElementById('mobile-sidebar').classList.contains('sidebar-open')) closeSidebar();
            else openSidebar();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });

        // Sidebar toggle (collapse/expand) – desktop only, persisted
        var STORAGE_KEY = 'managerSidebarCollapsed';
        function isSidebarCollapsed() {
            return document.getElementById('mobile-sidebar').classList.contains('sidebar-collapsed');
        }
        function setSidebarCollapsed(collapsed) {
            var sidebar = document.getElementById('mobile-sidebar');
            var iconCollapse = document.getElementById('sidebar-toggle-icon-collapse');
            var iconExpand = document.getElementById('sidebar-toggle-icon-expand');
            if (collapsed) {
                sidebar.classList.add('sidebar-collapsed');
                document.body.classList.add('sidebar-collapsed-main');
                if (iconCollapse) iconCollapse.classList.add('hidden');
                if (iconExpand) iconExpand.classList.remove('hidden');
                try { localStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                document.body.classList.remove('sidebar-collapsed-main');
                if (iconCollapse) iconCollapse.classList.remove('hidden');
                if (iconExpand) iconExpand.classList.add('hidden');
                try { localStorage.setItem(STORAGE_KEY, '0'); } catch (e) {}
            }
        }
        function toggleManagerSidebar() {
            setSidebarCollapsed(!isSidebarCollapsed());
        }
        function onMenuButtonClick() {
            if (!isSidebarVisible()) {
                openSidebar();
            } else {
                toggleManagerSidebar();
            }
        }
        document.getElementById('sidebar-toggle') && document.getElementById('sidebar-toggle').addEventListener('click', toggleManagerSidebar);
        document.getElementById('sidebar-toggle-top') && document.getElementById('sidebar-toggle-top').addEventListener('click', onMenuButtonClick);
        (function applySavedSidebarState() {
            try {
                if (localStorage.getItem(STORAGE_KEY) === '1') setSidebarCollapsed(true);
            } catch (e) {}
        })();

        window.addEventListener('load', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
