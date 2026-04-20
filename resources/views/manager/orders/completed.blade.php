<x-manager-layout>
    <x-slot name="header">
        Completed bookings
    </x-slot>

    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">Completed bookings</h2>
            <p class="text-sm font-medium text-white/40 uppercase tracking-wider mt-1">Paid bookings sorted by latest updates</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-cyan-500/20 to-cyan-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Revenue</p>
                <h3 class="text-3xl font-bold text-white tracking-tight">Tsh {{ number_format($stats['revenue'], 0) }}</h3>
            </div>
        </div>
        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Completed</p>
                <h3 class="text-3xl font-bold text-white tracking-tight">{{ number_format($stats['count']) }}</h3>
            </div>
        </div>
        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-violet-500/20 to-violet-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Avg value</p>
                <h3 class="text-3xl font-bold text-white tracking-tight">Tsh {{ number_format($stats['avg'], 0) }}</h3>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="glass-card p-6 rounded-2xl mb-8">
        <h3 class="text-lg font-bold text-white mb-4">Filters</h3>
        <form method="GET" action="{{ route('manager.orders.completed') }}" class="space-y-5">
            <div class="flex flex-wrap gap-2">
                @foreach([
                    'today' => 'Today',
                    '7' => 'Last 7 days',
                    '30' => 'Last 30 days',
                    '90' => 'Last 90 days',
                    'custom' => 'Custom range',
                ] as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="period" value="{{ $value }}" class="peer sr-only" {{ $period === $value ? 'checked' : '' }} @if($value !== 'custom') onchange="this.form.submit()" @endif>
                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold border border-white/10 bg-white/5 text-white/70 peer-checked:bg-gradient-to-r peer-checked:from-violet-600 peer-checked:to-cyan-600 peer-checked:text-white peer-checked:border-transparent transition-all">{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            @if($period === 'custom')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">From</label>
                        <input type="date" name="date_from" value="{{ $dateFromInput }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500 [color-scheme:dark]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">To</label>
                        <input type="date" name="date_to" value="{{ $dateToInput }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500 [color-scheme:dark]">
                    </div>
                </div>
            @else
                <input type="hidden" name="date_from" value="{{ $dateFromInput }}">
                <input type="hidden" name="date_to" value="{{ $dateToInput }}">
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">{{ config('salon.staff') }}</label>
                    <select name="waiter" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500 [&>option]:text-black">
                        <option value="all" {{ $waiterFilter === 'all' || $waiterFilter === '' ? 'selected' : '' }}>All</option>
                        @foreach($waiters as $w)
                            <option value="{{ $w->id }}" {{ (string) $waiterFilter === (string) $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Search</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ config('salon.seat') }}, client..." class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white placeholder-white/25 focus:ring-2 focus:ring-violet-500">
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all">
                    Apply
                </button>
                <a href="{{ route('manager.orders.completed') }}" class="glass px-6 py-2.5 rounded-xl font-semibold text-white/70 hover:text-white hover:bg-white/10 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($orders as $order)
            <div class="glass-card p-5 rounded-2xl card-hover border border-white/10">
                @include('manager.orders.partials.booking-card-summary', ['order' => $order, 'tone' => 'cyan'])
                <div class="mt-3 mb-4 rounded-xl border border-white/5 bg-black/25 overflow-hidden divide-y divide-white/[0.06]">
                    @foreach($order->items as $item)
                        <div class="flex justify-between gap-2 items-center text-sm px-3 py-2.5 hover:bg-white/[0.03]">
                            <span class="font-medium text-white/95 min-w-0 truncate">{{ $item->quantity }}× {{ $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Custom item') }}</span>
                            <span class="text-white/45 tabular-nums shrink-0 text-xs">Tsh {{ number_format($item->total) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-white/10">
                    <div>
                        <span class="block text-[9px] font-bold uppercase tracking-wider text-white/35">Paid</span>
                        <span class="text-base font-bold text-white tabular-nums">Tsh {{ number_format($order->total_amount) }}</span>
                    </div>
                    <span class="text-[10px] font-bold text-white/45 uppercase tracking-wider">Updated {{ $order->updated_at->timezone(config('app.timezone'))->format('H:i') }}</span>
                </div>
            </div>
        @empty
            <div class="col-span-full glass-card p-10 rounded-2xl text-center text-white/40">
                No completed bookings for this filter.
            </div>
        @endforelse
    </div>

    @if($orders->hasPages())
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
</x-manager-layout>

