<x-waiter-layout>
    <x-slot name="header">
        {{ config('salon.stylist_dash_page_title') }}
    </x-slot>

    <!-- Welcome Hero -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">{{ config('salon.stylist_dash_greeting') }}, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-white/50 font-medium mt-1">{{ config('salon.stylist_dash_subtitle') }}</p>
        </div>
        @auth
        @if(Auth::user()->restaurant_id)
        {{-- Online / Offline toggle --}}
        <div class="flex items-center gap-4">
            @if(Auth::user()->is_online)
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-sm font-semibold">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    {{ config('salon.stylist_online') }}
                </span>
                <form action="{{ route('waiter.status.update') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="is_online" value="0">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-rose-500/20 text-white/80 hover:text-rose-300 border border-white/10 text-sm font-semibold transition-all">{{ config('salon.stylist_btn_go_offline') }}</button>
                </form>
            @else
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-white/60 border border-white/10 text-sm font-semibold">
                    <span class="w-2 h-2 bg-white/50 rounded-full"></span>
                    {{ config('salon.stylist_offline') }}
                </span>
                <form action="{{ route('waiter.status.update') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="is_online" value="1">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold transition-all">{{ config('salon.stylist_btn_go_online') }}</button>
                </form>
            @endif
        </div>
        @endif
        @endauth
    </div>

    @if(isset($salaryNotifications) && $salaryNotifications->isNotEmpty())
    <div class="mb-6 space-y-3">
        @foreach($salaryNotifications as $n)
            @php $data = $n->data; @endphp
            <a href="{{ $data['url'] ?? route('waiter.salary-slip.index') }}" class="block p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-200 hover:bg-amber-500/20 transition-colors">
                <p class="font-semibold">Malipo yamethibitishwa – {{ $data['period_label'] ?? 'Salary Slip' }}</p>
                <p class="text-sm text-amber-200/80 mt-0.5">{{ $data['message'] ?? 'Angalia Salary Slip.' }}</p>
                <span class="text-xs text-amber-400 mt-2 inline-block">Angalia slip →</span>
            </a>
        @endforeach
    </div>
    @endif

    <!-- Bento Grid Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        
        <!-- 1. Tips Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-600 to-orange-700 p-5 text-white shadow-lg shadow-amber-500/20 group card-hover">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/10 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="absolute -bottom-10 -left-10 h-24 w-24 rounded-full bg-white/5 transition-transform duration-700 group-hover:scale-150"></div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-start justify-between">
                    <div class="rounded-xl bg-white/20 p-2.5 backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                            <circle cx="8" cy="8" r="6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18"/><path d="M7 6h1v4"/><path d="m16.71 13.88.7.71-2.82 2.82"/>
                        </svg>
                    </div>
                    <span class="rounded-full bg-white/20 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-white backdrop-blur-sm">{{ config('salon.stylist_stat_badge_today') }}</span>
                </div>
                <div class="mt-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-amber-100/80">{{ config('salon.stylist_stat_tips_label') }}</p>
                    <h3 class="mt-1 text-2xl font-bold tracking-tight" id="stat-tips-today">Tsh {{ number_format($tipsToday) }}</h3>
                </div>
            </div>
        </div>

        <!-- 2. Active Orders -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 to-indigo-700 p-5 text-white shadow-lg shadow-violet-500/20 group card-hover">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/10 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="absolute -bottom-10 -left-10 h-24 w-24 rounded-full bg-white/5 transition-transform duration-700 group-hover:scale-150"></div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-start justify-between">
                    <div class="rounded-xl bg-white/20 p-2.5 backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                    </div>
                    <span class="rounded-full bg-white/20 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-white backdrop-blur-sm flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                        {{ config('salon.stylist_stat_live_badge') }}
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-violet-100/80">My {{ config('salon.booking_plural') }}</p>
                    <h3 class="mt-1 text-2xl font-bold tracking-tight" id="stat-my-active-orders">{{ $myActiveOrders }}</h3>
                </div>
            </div>
        </div>

        <!-- 3. Ready to Serve -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 p-5 text-white shadow-lg shadow-emerald-500/20 group card-hover">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/10 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="absolute -bottom-10 -left-10 h-24 w-24 rounded-full bg-white/5 transition-transform duration-700 group-hover:scale-150"></div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-start justify-between">
                    <div class="rounded-xl bg-white/20 p-2.5 backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                            <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>
                        </svg>
                    </div>
                    @if($readyToServeOrders > 0)
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                        </span>
                    @endif
                </div>
                <div class="mt-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-100/80">{{ config('salon.stylist_stat_ready_label') }}</p>
                    <h3 class="mt-1 text-2xl font-bold tracking-tight" id="stat-ready-to-serve">{{ $readyToServeOrders }}</h3>
                </div>
            </div>
        </div>

        <!-- 4. Pending Calls -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-600 to-pink-700 p-5 text-white shadow-lg shadow-rose-500/20 group card-hover">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/10 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="absolute -bottom-10 -left-10 h-24 w-24 rounded-full bg-white/5 transition-transform duration-700 group-hover:scale-150"></div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-start justify-between">
                    <div class="rounded-xl bg-white/20 p-2.5 backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                        </svg>
                    </div>
                    @if($pendingRequests->count() > 0)
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                        </span>
                    @endif
                </div>
                <div class="mt-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-rose-100/80">{{ config('salon.stylist_stat_calls_label') }}</p>
                    <h3 class="mt-1 text-2xl font-bold tracking-tight" id="stat-pending-requests">{{ $pendingRequests->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left Column: Active Orders & Requests -->
        <div class="xl:col-span-2 space-y-6">
            
            <!-- Urgent Requests -->
            @if($pendingRequests->count() > 0)
            <section id="requests">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ config('salon.stylist_section_urgent') }}</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($pendingRequests as $request)
                        <div class="relative overflow-hidden rounded-2xl glass-card p-5 border {{ $request->type == 'request_bill' ? 'border-violet-500/20' : 'border-rose-500/20' }} card-hover">
                            <div class="absolute -top-10 -right-10 w-24 h-24 {{ $request->type == 'request_bill' ? 'bg-violet-500/10' : 'bg-rose-500/10' }} rounded-full blur-2xl"></div>
                            <div class="flex items-start justify-between mb-4 relative z-10">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-xl {{ $request->type == 'request_bill' ? 'bg-violet-500/20 text-violet-400 border border-violet-500/20' : 'bg-rose-500/20 text-rose-400 border border-rose-500/20' }}">
                                        @if($request->type == 'request_bill')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8h-6"/><path d="M16 12h-6"/><path d="M16 16h-6"/>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="animate-bounce">
                                                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/><path d="M4 2C2.8 3.7 2 5.7 2 8"/><path d="M22 8c0-2.3-.8-4.3-2-6"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-white">{{ config('salon.seat') }} #{{ $request->table_number }}</h4>
                                        <p class="text-[10px] font-medium uppercase tracking-widest text-white/40">{{ $request->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-5 relative z-10">
                                <p class="text-sm font-semibold {{ $request->type == 'request_bill' ? 'text-violet-400' : 'text-rose-400' }}">
                                    {{ $request->type == 'request_bill' ? config('salon.stylist_request_bill') : config('salon.stylist_request_call') }}
                                </p>
                            </div>
                            <form action="{{ route('waiter.requests.complete', $request->id) }}" method="POST" class="relative z-10">
                                @csrf
                                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-cyan-600 py-3 text-[11px] font-bold uppercase tracking-widest text-white transition-all hover:shadow-lg hover:shadow-violet-500/25">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>
                                    {{ config('salon.stylist_mark_done') }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Unassigned bookings -->
            @if($unassignedOrders->count() > 0)
            <section id="unassigned-orders">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ config('salon.stylist_unassigned_title') }}</h3>
                    <span class="bg-amber-500/20 text-amber-400 text-[10px] font-bold px-2 py-1 rounded-md border border-amber-500/20 uppercase tracking-widest">{{ config('salon.stylist_action_required') }}</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($unassignedOrders as $order)
                        <div class="glass-card p-5 rounded-2xl border border-white/5 card-hover group">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center font-bold text-white border border-white/10">
                                        {{ $order->table_number }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-white">Booking #{{ $order->id }}</h4>
                                        <p class="text-[10px] text-white/40 uppercase font-bold tracking-widest">{{ $order->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-white/60 bg-white/5 px-2 py-1 rounded-lg border border-white/10 uppercase tracking-widest">{{ $order->status }}</span>
                            </div>
                            
                            <div class="space-y-2 mb-5">
                                @foreach($order->items->take(2) as $item)
                                    <div class="flex justify-between text-xs">
                                        <span class="text-white/60">{{ $item->quantity }}x {{ $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Custom service') }}</span>
                                    </div>
                                @endforeach
                                @if($order->items->count() > 2)
                                    <p class="text-[10px] text-white/30 font-bold italic">+{{ $order->items->count() - 2 }} {{ config('salon.stylist_more_lines') }}</p>
                                @endif
                            </div>

                            <form action="{{ route('waiter.orders.claim', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-3 bg-white text-black rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-violet-500 hover:text-white transition-all shadow-lg shadow-white/5">
                                    {{ config('salon.stylist_claim_booking') }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Active Orders List -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ config('salon.stylist_active_bookings_title') }}</h3>
                    <a href="{{ route('waiter.orders') }}" class="text-[11px] font-bold text-violet-400 hover:text-violet-300 uppercase tracking-widest">{{ config('salon.stylist_view_all') }}</a>
                </div>
                
                <div class="rounded-2xl glass-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-white/[0.02]">
                                <tr>
                                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-white/40">{{ config('salon.seat') }}</th>
                                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-white/40">{{ config('salon.stylist_table_col_booking') }}</th>
                                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-white/40">{{ config('salon.stylist_table_col_status') }}</th>
                                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-white/40">{{ config('salon.stylist_table_col_total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse($myOrders as $order)
                                    <tr class="group hover:bg-white/[0.02] transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500/20 to-cyan-500/20 font-bold text-violet-400 transition-all group-hover:from-violet-600 group-hover:to-cyan-600 group-hover:text-white text-sm border border-violet-500/20 group-hover:border-transparent">
                                                {{ $order->table_number }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-white">#{{ $order->id }}</span>
                                                <span class="text-[10px] font-medium text-white/40">{{ $order->items->count() }} {{ config('salon.stylist_table_lines_meta') }} • {{ $order->created_at->format('H:i') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest 
                                                {{ $order->status == 'ready' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 
                                                  ($order->status == 'served' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20') }}">
                                                {{ data_get(config('salon.stylist_booking_status_display'), $order->status, $order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-bold text-sm text-white">Tsh {{ number_format($order->total_amount) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-12 text-center">
                                            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/5 border border-white/5">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/20">
                                                    <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm font-semibold text-white/50">{{ config('salon.stylist_no_bookings_today') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <!-- Right Column: Stats & Pulse -->
        <div class="space-y-6">
            <!-- Saloon pulse -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-surface-900 to-surface-800 p-6 text-white shadow-xl border border-white/5">
                <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-violet-500/10 blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 h-56 w-56 rounded-full bg-cyan-500/10 blur-3xl"></div>
                
                <div class="relative z-10">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="text-lg font-bold tracking-tight">{{ config('salon.stylist_pulse_title') }}</h3>
                        <span class="flex h-2.5 w-2.5 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-xl bg-white/5 p-4 backdrop-blur-sm border border-white/5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-white/40">{{ config('salon.stylist_pulse_total_active') }}</p>
                            <p class="mt-1 text-2xl font-bold">{{ $restaurantActiveOrders }}</p>
                        </div>
                        <div class="rounded-xl bg-gradient-to-br from-violet-600 to-cyan-600 p-4 shadow-lg shadow-violet-500/20">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-white/80">{{ config('salon.stylist_pulse_ready') }}</p>
                            <p class="mt-1 text-2xl font-bold">{{ $readyToServeOrders }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Service Tag & QR -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-900/50 to-violet-900/50 p-6 text-white shadow-xl border border-cyan-500/20">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold tracking-tight">{{ config('salon.stylist_service_tag_title') }}</h3>
                    <div class="rounded-lg bg-cyan-500/20 px-2 py-1 text-cyan-400 border border-cyan-500/30">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/>
                        </svg>
                    </div>
                </div>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="bg-white p-2 rounded-xl shadow-lg shadow-black/20">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(Auth::user()->waiter_qr_url) }}" alt="My QR" class="w-20 h-20">
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-white/40 mb-1">{{ config('salon.stylist_your_code_label') }}</p>
                        <p class="text-2xl font-mono font-bold text-cyan-400 tracking-wider">{{ Auth::user()->waiter_code ?? '—' }}</p>
                        <p class="text-[10px] text-white/40 mt-1">{{ config('salon.stylist_share_booking_hint') }}</p>
                    </div>
                </div>

                <div class="mb-4 rounded-xl border border-white/10 bg-white/[0.03] p-3 text-[11px] leading-relaxed text-white/55 space-y-2">
                    <p class="font-bold text-white/80 text-xs">{{ config('salon.code_legend_title') }}</p>
                    <p>{{ config('salon.code_legend_tiptap_id') }}</p>
                    <p>{{ config('salon.code_legend_salon_tag') }}</p>
                    <p>{{ config('salon.code_legend_whatsapp_qr') }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <a href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode(Auth::user()->waiter_qr_url) }}" download="my-qr-{{ Auth::user()->waiter_code }}.png" target="_blank" class="flex items-center justify-center gap-2 rounded-xl bg-white/10 py-2.5 text-[11px] font-bold uppercase tracking-widest hover:bg-white/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                        {{ config('salon.stylist_save_qr') }}
                    </a>
                    <button onclick="copyToClipboard('{{ Auth::user()->waiter_qr_url }}', this)" class="flex items-center justify-center gap-2 rounded-xl bg-cyan-600 py-2.5 text-[11px] font-bold uppercase tracking-widest hover:bg-cyan-500 transition-all shadow-lg shadow-cyan-600/20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                        {{ config('salon.stylist_copy_link') }}
                    </button>
                </div>
            </div>

            @if(!empty($hasOrderPortalAccess) && !empty($orderPortalLoginUrl))
            <!-- Service desk portal -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-900/50 to-cyan-900/50 p-6 text-white shadow-xl border border-violet-500/20">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-lg font-bold tracking-tight">TIPTAP · {{ config('salon.portal_order') }}</h3>
                    <span class="rounded-lg bg-emerald-500/20 px-2 py-1 text-emerald-400 border border-emerald-500/30 text-[10px] font-bold uppercase">{{ config('salon.stylist_order_portal_badge') }}</span>
                </div>
                <p class="text-sm text-white/70 mb-4">Una ufikiaji wa {{ strtolower(config('salon.live_bookings')) }}. Ingia kwa <strong class="text-cyan-300">nambari yako ya {{ strtolower(config('salon.staff')) }}</strong> ({{ Auth::user()->global_waiter_number ?? Auth::user()->waiter_code ?? '—' }}) na <strong class="text-cyan-300">nenosiri uliyopewa na manager</strong>.</p>
                <a href="{{ $orderPortalLoginUrl }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 hover:bg-violet-500 py-2.5 px-4 text-sm font-semibold transition-all shadow-lg shadow-violet-600/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    {{ config('salon.stylist_order_portal_open') }}
                </a>
            </div>
            @endif

            <!-- Recent Ratings -->
            <div class="rounded-2xl glass-card p-6">
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white tracking-tight">{{ config('salon.stylist_feedback_recent') }}</h3>
                    <div class="flex items-center gap-1.5 rounded-lg bg-amber-500/10 px-2.5 py-1.5 text-amber-400 border border-amber-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        <span class="text-sm font-bold">4.8</span>
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse($recentFeedback as $feedback)
                        <div class="rounded-xl glass p-4 transition-colors hover:bg-white/[0.03]">
                            <div class="mb-2 flex items-center justify-between">
                                <div class="flex text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="{{ $i <= $feedback->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $i <= $feedback->rating ? '' : 'text-white/20' }}">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-[9px] font-medium uppercase text-white/40">{{ $feedback->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm font-medium italic text-white/60">"{{ $feedback->comment }}"</p>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-white/40">{{ config('salon.stylist_no_ratings_yet') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <script>
        // Auto-refresh stats every 30 seconds
        setInterval(function() {
            fetch('{{ route("waiter.dashboard.stats") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('stat-tips-today').textContent = 'Tsh ' + new Intl.NumberFormat().format(data.tips_today);
                    document.getElementById('stat-my-active-orders').textContent = data.my_active_orders;
                    document.getElementById('stat-ready-to-serve').textContent = data.ready_to_serve;
                    document.getElementById('stat-pending-requests').textContent = data.pending_requests;
                })
                .catch(error => console.error('Error fetching stats:', error));
        }, 30000); // 30 seconds

        async function copyToClipboard(text, button) {
            try {
                await navigator.clipboard.writeText(text);
                
                // Visual feedback
                const originalContent = button.innerHTML;
                const copiedLabel = @json(config('salon.stylist_copy_copied'));
                button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M20 6 9 17l-5-5"/></svg> ${copiedLabel}`;
                
                setTimeout(() => {
                    button.innerHTML = originalContent;
                }, 2000);
                
            } catch (err) {
                console.error('Failed to copy:', err);
                alert(@json(config('salon.stylist_copy_failed')));
            }
        }
    </script>
</x-waiter-layout>
