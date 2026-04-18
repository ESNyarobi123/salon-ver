<x-manager-layout>
    <x-slot name="header">
        {{ config('salon.manager_dash_page_title') }}
    </x-slot>

    @if(session('info'))
        <div class="mb-6 p-4 bg-white/10 border border-white/20 rounded-xl">
            <p class="text-sm font-medium text-white/80">{{ session('info') }}</p>
        </div>
    @endif

    @include('manager.partials.quick-links')

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Stat 1: Orders -->
        <div class="glass-card rounded-2xl p-6 card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-violet-500/20 to-violet-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-5">
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500/20 to-purple-500/20 rounded-xl flex items-center justify-center border border-violet-500/20 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet-400">
                            <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1.5 bg-violet-500/10 text-violet-400 text-[10px] font-bold rounded-full uppercase tracking-wider border border-violet-500/20">Today</span>
                </div>
                <p class="text-[11px] font-semibold text-white/40 uppercase tracking-wider mb-1">Bookings today</p>
                <h3 class="text-3xl font-bold text-white tracking-tight" id="stat-total-orders">{{ number_format($totalOrdersToday) }}</h3>
            </div>
        </div>

        <!-- Stat 2: Revenue -->
        <div class="glass-card rounded-2xl p-6 card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-cyan-500/20 to-cyan-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-5">
                    <div class="w-12 h-12 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center border border-cyan-500/20 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400">
                            <line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1.5 bg-cyan-500/10 text-cyan-400 text-[10px] font-bold rounded-full uppercase tracking-wider border border-cyan-500/20">Revenue</span>
                </div>
                <p class="text-[11px] font-semibold text-white/40 uppercase tracking-wider mb-1">Revenue Today</p>
                <h3 class="text-3xl font-bold text-white tracking-tight" id="stat-revenue-today">Tsh {{ number_format($revenueToday) }}</h3>
            </div>
        </div>

        <!-- Stat 3: Rating -->
        <div class="glass-card rounded-2xl p-6 card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-amber-500/20 to-amber-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-5">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-xl flex items-center justify-center border border-amber-500/20 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1.5 bg-amber-500/10 text-amber-400 text-[10px] font-bold rounded-full uppercase tracking-wider border border-amber-500/20">Rating</span>
                </div>
                <p class="text-[11px] font-semibold text-white/40 uppercase tracking-wider mb-1">Avg. Rating</p>
                <h3 class="text-3xl font-bold text-white tracking-tight" id="stat-avg-rating">{{ number_format($avgRating, 1) }}/5.0</h3>
            </div>
        </div>

        <!-- Stat 4: Stylists -->
        <div class="glass-card rounded-2xl p-6 card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-5">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-xl flex items-center justify-center border border-emerald-500/20 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-400">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1.5 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold rounded-full uppercase tracking-wider border border-emerald-500/20 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                        Online
                    </span>
                </div>
                <p class="text-[11px] font-semibold text-white/40 uppercase tracking-wider mb-1">{{ config('salon.staff_plural') }} online</p>
                <h3 class="text-3xl font-bold text-white tracking-tight" id="stat-waiters-online">{{ $waitersOnline }} {{ config('salon.manager_staff_online_suffix') }}</h3>
            </div>
        </div>
    </div>

    <!-- Smart live booking tracking -->
    <div class="mb-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-2xl font-bold text-white tracking-tight">Live {{ config('salon.booking_plural') }}</h3>
                <p class="text-sm font-medium text-white/40 uppercase tracking-wider">Real-time {{ strtolower(config('salon.floor_display_short')) }} &amp; service status</p>
            </div>
            <div class="flex gap-3">
                <button onclick="window.location.reload()" class="glass px-4 py-2.5 rounded-xl font-semibold text-white/70 hover:text-white hover:bg-white/10 transition-all flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/>
                    </svg>
                    Refresh
                </button>
                <a href="{{ route('manager.orders.live') }}" class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                    </svg>
                    Full Screen
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Pending Column -->
            <div class="glass-card p-5 rounded-2xl">
                <div class="flex items-center justify-between mb-5 px-1">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></div>
                        <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Pending</h4>
                    </div>
                    <span class="bg-rose-500/10 text-rose-400 text-[11px] font-bold px-2.5 py-1 rounded-full border border-rose-500/20">{{ $pendingOrders->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse($pendingOrders->take(3) as $order)
                        <div class="glass p-4 rounded-xl card-hover">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-bold text-white">{{ config('salon.seat') }} #{{ $order->table_number }}</span>
                                <span class="text-[10px] font-medium text-white/40">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex -space-x-2 mb-3">
                                @foreach($order->items->take(3) as $item)
                                    @php
                                        $itemName = $item->menuItem ? $item->menuItem->name : ($item->name ?? 'Item');
                                        $itemImageUrl = $item->menuItem ? $item->menuItem->imageUrl() : null;
                                    @endphp
                                    <div class="w-7 h-7 rounded-full border-2 border-surface-900 bg-white/10 flex items-center justify-center text-[9px] font-bold text-white overflow-hidden" title="{{ $itemName }}">
                                        @if($itemImageUrl)
                                            <img src="{{ $itemImageUrl }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            {{ substr($itemName, 0, 1) }}
                                        @endif
                                    </div>
                                @endforeach
                                @if($order->items->count() > 3)
                                    <div class="w-7 h-7 rounded-full border-2 border-surface-900 bg-white/5 flex items-center justify-center text-[8px] font-bold text-white/50">
                                        +{{ $order->items->count() - 3 }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[11px] font-bold text-rose-400">Tsh {{ number_format($order->total_amount) }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/20">
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/20 mx-auto mb-2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <p class="text-[11px] font-bold text-white/40 uppercase tracking-wider">All Clear</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Preparing Column -->
            <div class="glass-card p-5 rounded-2xl">
                <div class="flex items-center justify-between mb-5 px-1">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></div>
                        <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Preparing</h4>
                    </div>
                    <span class="bg-amber-500/10 text-amber-400 text-[11px] font-bold px-2.5 py-1 rounded-full border border-amber-500/20">{{ $preparingOrders->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse($preparingOrders->take(3) as $order)
                        <div class="glass p-4 rounded-xl card-hover">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-bold text-white">{{ config('salon.seat') }} #{{ $order->table_number }}</span>
                                <div class="flex items-center gap-1 text-amber-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <span class="text-[10px] font-bold">In service</span>
                                </div>
                            </div>
                            <div class="w-full bg-white/5 h-1.5 rounded-full mb-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-500 to-amber-400 h-full rounded-full animate-pulse" style="width: 65%"></div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[11px] font-bold text-white">{{ $order->items->count() }} Items</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/20">
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/20 mx-auto mb-2">
                                <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>
                            </svg>
                            <p class="text-[11px] font-bold text-white/40 uppercase tracking-wider">No active services</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Served Column -->
            <div class="glass-card p-5 rounded-2xl">
                <div class="flex items-center justify-between mb-5 px-1">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                        <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Served</h4>
                    </div>
                    <span class="bg-emerald-500/10 text-emerald-400 text-[11px] font-bold px-2.5 py-1 rounded-full border border-emerald-500/20">{{ $servedOrders->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse($servedOrders->take(3) as $order)
                        <div class="glass p-4 rounded-xl card-hover">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-bold text-white">{{ config('salon.seat') }} #{{ $order->table_number }}</span>
                                <span class="bg-emerald-500/10 text-emerald-400 text-[9px] font-bold px-2 py-0.5 rounded-full uppercase border border-emerald-500/20">Ready to Pay</span>
                            </div>
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-7 h-7 bg-violet-500/20 rounded-lg flex items-center justify-center border border-violet-500/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet-400">
                                        <rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>
                                    </svg>
                                </div>
                                <p class="text-[11px] font-medium text-white/40">Waiting for payment</p>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[11px] font-bold text-white">Tsh {{ number_format($order->total_amount) }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/20">
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/20 mx-auto mb-2">
                                <circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" x2="9.01" y1="9" y2="9"/><line x1="15" x2="15.01" y1="9" y2="9"/>
                            </svg>
                            <p class="text-[11px] font-bold text-white/40 uppercase tracking-wider">Nothing ready yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Paid Column -->
            <div class="glass-card p-5 rounded-2xl">
                <div class="flex items-center justify-between mb-5 px-1">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-cyan-500 rounded-full"></div>
                        <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Completed</h4>
                    </div>
                    <span class="bg-cyan-500/10 text-cyan-400 text-[11px] font-bold px-2.5 py-1 rounded-full border border-cyan-500/20">{{ $paidOrders->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse($paidOrders->take(3) as $order)
                        <div class="glass p-4 rounded-xl opacity-60">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-bold text-white">{{ config('salon.seat') }} #{{ $order->table_number }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-400">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <p class="text-[11px] font-medium text-white/40">Completed {{ $order->updated_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/20 mx-auto mb-2">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <p class="text-[11px] font-bold text-white/40 uppercase tracking-wider">No History</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Feedback & Messages -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- {{ config('salon.customer') }} feedback -->
        <div class="glass-card p-6 rounded-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white tracking-tight">Recent Feedback</h3>
                <a href="{{ route('manager.feedback.index') }}" class="text-[11px] font-bold text-violet-400 hover:text-violet-300 uppercase tracking-wider">View All</a>
            </div>
            <div class="space-y-4">
                @forelse($recentFeedback as $feedback)
                    <div class="flex gap-4 p-4 glass rounded-xl card-hover">
                        <div class="w-11 h-11 bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-xl flex items-center justify-center font-bold text-violet-400 border border-violet-500/20">
                            {{ substr($feedback->order->customer_name ?? $feedback->waiter->name ?? 'C', 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between mb-1">
                                <h5 class="font-semibold text-white">{{ $feedback->order->customer_name ?? 'Feedback for ' . ($feedback->waiter->name ?? 'Service') }}</h5>
                                <div class="flex text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="{{ $i <= $feedback->rating ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $i <= $feedback->rating ? '' : 'text-white/20' }}">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm text-white/60 italic">"{{ $feedback->comment }}"</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-white/40 text-center py-8">No feedback yet</p>
                @endforelse
            </div>
        </div>

        {{-- Tips are not shown to manager --}}
    </div>
    <script>
        // Auto-refresh stats every 30 seconds
        setInterval(function() {
            fetch('{{ route("manager.dashboard.stats") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('stat-total-orders').textContent = new Intl.NumberFormat().format(data.total_orders_today);
                    document.getElementById('stat-revenue-today').textContent = 'Tsh ' + new Intl.NumberFormat().format(data.revenue_today);
                    document.getElementById('stat-avg-rating').textContent = data.avg_rating + '/5.0';
                    document.getElementById('stat-waiters-online').textContent = data.waiters_online_label;
                })
                .catch(error => console.error('Error fetching stats:', error));
        }, 30000); // 30 seconds
    </script>
</x-manager-layout>
