<x-waiter-layout>
    <x-slot name="header">Active {{ config('salon.booking_plural') }}</x-slot>

    <!-- Quick Stats Bar -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="glass-card p-3 rounded-xl border border-white/10">
            <p class="text-[9px] font-bold text-white/40 uppercase tracking-wider mb-1">Ready Now</p>
            <p class="text-2xl font-bold text-emerald-400" id="readyCount">{{ $orders->where('status', 'ready')->count() }}</p>
        </div>
        <div class="glass-card p-3 rounded-xl border border-white/10">
            <p class="text-[9px] font-bold text-white/40 uppercase tracking-wider mb-1">In progress</p>
            <p class="text-2xl font-bold text-amber-400" id="cookingCount">{{ $orders->whereIn('status', ['pending', 'preparing'])->count() }}</p>
        </div>
        <div class="glass-card p-3 rounded-xl border border-white/10">
            <p class="text-[9px] font-bold text-white/40 uppercase tracking-wider mb-1">Served</p>
            <p class="text-2xl font-bold text-cyan-400" id="servedCount">{{ $orders->where('status', 'served')->count() }}</p>
        </div>
        <div class="glass-card p-3 rounded-xl border border-white/10">
            <p class="text-[9px] font-bold text-white/40 uppercase tracking-wider mb-1">Total</p>
            <p class="text-2xl font-bold text-white" id="totalCount">{{ $orders->count() }}</p>
        </div>
    </div>

    @php
        $readyOrders = $orders->where('status', 'ready');
        $inProgressOrders = $orders->whereIn('status', ['pending', 'preparing']);
        $servedOrders = $orders->where('status', 'served');
    @endphp

    <!-- Ready to Serve (Priority) -->
    @if($readyOrders->count() > 0)
    <section class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex h-2.5 w-2.5 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </div>
            <h2 class="text-lg font-bold text-white">Ready to Serve</h2>
            <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[10px] font-bold rounded-full border border-emerald-500/30">{{ $readyOrders->count() }}</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($readyOrders as $order)
                <div class="glass-card p-4 rounded-xl border-2 border-emerald-500/30 bg-emerald-500/5 card-hover group">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500/30 to-teal-500/30 rounded-lg flex items-center justify-center font-bold text-xl text-emerald-400 border border-emerald-500/30 group-hover:scale-110 transition-transform">
                                {{ $order->table_number }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">Booking #{{ $order->id }}</p>
                                <p class="text-[9px] text-white/40 uppercase font-bold tracking-wider">{{ $order->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 text-[9px] font-bold rounded-lg border border-emerald-500/30 uppercase flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                            Ready
                        </span>
                    </div>
                    <div class="mb-3 space-y-1.5">
                        @foreach($order->items->take(3) as $item)
                            <div class="flex justify-between text-xs">
                                <span class="text-white/70">{{ $item->quantity }}x {{ $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Item') }}</span>
                            </div>
                        @endforeach
                        @if($order->items->count() > 3)
                            <p class="text-[9px] text-white/30 font-bold italic">+ {{ $order->items->count() - 3 }} more</p>
                        @endif
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-white/10">
                        <p class="text-sm font-bold text-white">Tsh {{ number_format($order->total_amount) }}</p>
                        <button class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold rounded-lg transition-all uppercase tracking-wider">
                            Serve Now
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- In Progress -->
    @if($inProgressOrders->count() > 0)
    <section class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <h2 class="text-lg font-bold text-white">In progress</h2>
            <span class="px-2 py-0.5 bg-amber-500/20 text-amber-400 text-[10px] font-bold rounded-full border border-amber-500/30">{{ $inProgressOrders->count() }}</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($inProgressOrders as $order)
                <div class="glass-card p-4 rounded-xl border border-amber-500/20 card-hover group">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-lg flex items-center justify-center font-bold text-xl text-amber-400 border border-amber-500/20 group-hover:scale-110 transition-transform">
                                {{ $order->table_number }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">Booking #{{ $order->id }}</p>
                                <p class="text-[9px] text-white/40 uppercase font-bold tracking-wider">{{ $order->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 bg-amber-500/20 text-amber-400 text-[9px] font-bold rounded-lg border border-amber-500/20 uppercase">{{ $order->status }}</span>
                    </div>
                    <div class="mb-3 space-y-1.5">
                        @foreach($order->items->take(3) as $item)
                            <div class="flex justify-between text-xs">
                                <span class="text-white/70">{{ $item->quantity }}x {{ $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Item') }}</span>
                            </div>
                        @endforeach
                        @if($order->items->count() > 3)
                            <p class="text-[9px] text-white/30 font-bold italic">+ {{ $order->items->count() - 3 }} more</p>
                        @endif
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-white/10">
                        <p class="text-sm font-bold text-white">Tsh {{ number_format($order->total_amount) }}</p>
                        <div class="flex items-center gap-1 text-amber-400 text-[9px] font-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="animate-spin">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            Cooking...
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Served Orders -->
    @if($servedOrders->count() > 0)
    <section class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <h2 class="text-lg font-bold text-white">Served</h2>
            <span class="px-2 py-0.5 bg-cyan-500/20 text-cyan-400 text-[10px] font-bold rounded-full border border-cyan-500/30">{{ $servedOrders->count() }}</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($servedOrders as $order)
                <div class="glass-card p-3 rounded-lg border border-white/10 hover:border-cyan-500/30 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-lg flex items-center justify-center font-bold text-sm text-cyan-400 border border-cyan-500/20">
                                {{ $order->table_number }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">#{{ $order->id }}</p>
                                <p class="text-[8px] text-white/40">{{ $order->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-cyan-400">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-white/80">Tsh {{ number_format($order->total_amount) }}</p>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    @if($orders->count() === 0)
        <div class="glass-card p-16 rounded-2xl text-center">
            <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/5">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white/20">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">No active {{ strtolower(config('salon.booking_plural')) }}</h3>
            <p class="text-sm text-white/40 mb-4">You don&apos;t have any active {{ strtolower(config('salon.booking_plural')) }} right now.</p>
            <a href="{{ route('waiter.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-xl transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                Go to Dashboard
            </a>
        </div>
    @endif
</x-waiter-layout>
