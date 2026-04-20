<x-manager-layout>
    <x-slot name="header">
        {{ config('salon.manager_stock_page_title') }}
    </x-slot>

    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">{{ config('salon.manager_stock_page_title') }}</h2>
            <p class="text-sm font-medium text-white/40 uppercase tracking-wider mt-1">{{ config('salon.manager_stock_page_subtitle') }}</p>
        </div>
        <a href="{{ route('manager.menu.index') }}" class="inline-flex items-center justify-center gap-2 shrink-0 rounded-xl px-5 py-3 text-sm font-semibold text-white bg-gradient-to-r from-violet-600 to-cyan-600 shadow-lg shadow-violet-500/20 hover:shadow-violet-500/35 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            {{ config('salon.manager_stock_open_catalog') }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl border border-emerald-500/25 bg-emerald-500/10">
            <p class="text-sm font-medium text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl border border-rose-500/25 bg-rose-500/10">
            <p class="text-sm font-medium text-rose-300">{{ $errors->first() }}</p>
        </div>
    @endif

    {{-- Status overview --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="glass-card rounded-2xl p-5 relative overflow-hidden group card-hover">
            <div class="absolute -top-8 -right-8 w-24 h-24 bg-violet-500/20 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2">{{ config('salon.manager_stock_stat_total') }}</p>
                <p class="text-3xl font-bold text-white tabular-nums">{{ $totalLines }}</p>
            </div>
        </div>
        <div class="glass-card rounded-2xl p-5 relative overflow-hidden group card-hover border border-emerald-500/15">
            <div class="absolute -top-8 -right-8 w-24 h-24 bg-emerald-500/15 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 mb-2">{{ config('salon.manager_stock_stat_healthy') }}</p>
                <p class="text-3xl font-bold text-emerald-300 tabular-nums">{{ $healthyCount }}</p>
            </div>
        </div>
        <div class="glass-card rounded-2xl p-5 relative overflow-hidden group card-hover border border-amber-500/15">
            <div class="absolute -top-8 -right-8 w-24 h-24 bg-amber-500/15 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold uppercase tracking-wider text-amber-400/90 mb-2">{{ config('salon.manager_stock_stat_low') }}</p>
                <p class="text-3xl font-bold text-amber-300 tabular-nums">{{ $lowStockCount }}</p>
            </div>
        </div>
        <div class="glass-card rounded-2xl p-5 relative overflow-hidden group card-hover border border-rose-500/15">
            <div class="absolute -top-8 -right-8 w-24 h-24 bg-rose-500/15 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold uppercase tracking-wider text-rose-400/90 mb-2">{{ config('salon.manager_stock_stat_out') }}</p>
                <p class="text-3xl font-bold text-rose-300 tabular-nums">{{ $outOfStockCount }}</p>
            </div>
        </div>
    </div>

    {{-- Overall health banner --}}
    @if($overallStatus === 'empty')
        <div class="glass-card rounded-2xl p-6 mb-8 border border-white/10 bg-white/[0.03]">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white/50"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">{{ config('salon.manager_stock_status_empty_title') }}</h3>
                    <p class="text-sm text-white/50 mt-1">{{ config('salon.manager_stock_status_empty_body') }}</p>
                </div>
            </div>
        </div>
    @elseif($overallStatus === 'healthy')
        <div class="rounded-2xl p-6 mb-8 border border-emerald-500/30 bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-transparent">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-400"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-emerald-100">{{ config('salon.manager_stock_status_healthy_title') }}</h3>
                    <p class="text-sm text-emerald-200/70 mt-1">{{ config('salon.manager_stock_status_healthy_body') }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-2xl p-6 mb-8 border border-amber-500/35 bg-gradient-to-br from-amber-500/15 via-amber-500/5 to-rose-500/10">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/25 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-amber-300"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-amber-100">{{ config('salon.manager_stock_status_attention_title') }}</h3>
                    <p class="text-sm text-amber-200/75 mt-1">{{ config('salon.manager_stock_status_attention_body') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-8 max-w-3xl space-y-1">
        <p class="text-sm text-white/50">{{ config('salon.manager_stock_info_short') }}</p>
        <p class="text-xs text-white/35">{{ str_replace(':count', (string) $trackedCount, config('salon.manager_stock_tracked_summary')) }}</p>
    </div>

    @if($lowStockItems->isNotEmpty())
        <div class="glass-card p-5 rounded-2xl border border-amber-500/25 mb-8">
            <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                Quick list — low or out
            </h3>
            <ul class="flex flex-wrap gap-2">
                @foreach($lowStockItems as $item)
                    <li class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/5 text-xs text-white/90 border border-white/10">
                        <span class="font-medium truncate max-w-[10rem]">{{ $item->name }}</span>
                        <span class="text-amber-400 font-mono shrink-0">{{ $item->stock_quantity }} / ≤{{ $item->low_stock_threshold }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="space-y-4">
        @forelse($menuItems as $item)
            @php
                $health = $item->stockHealth();
            @endphp
            <div @class([
                'glass-card rounded-2xl overflow-hidden transition-shadow',
                'ring-1 ring-rose-500/35 shadow-lg shadow-rose-500/5' => $health === 'out',
                'ring-1 ring-amber-500/35' => $health === 'low',
                'ring-1 ring-emerald-500/20' => $health === 'ok',
            ])>
                <div class="flex flex-col lg:flex-row lg:items-stretch gap-0">
                    <div @class([
                        'w-full lg:w-1.5 shrink-0',
                        'bg-rose-500' => $health === 'out',
                        'bg-amber-500' => $health === 'low',
                        'bg-emerald-500/70' => $health === 'ok',
                    ])></div>
                    <div class="flex-1 p-5 lg:p-6 flex flex-col lg:flex-row lg:items-center gap-5">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-cyan-500/15 text-cyan-300 border border-cyan-500/25">{{ config('salon.category_catalog_kind_product') }}</span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-white/35">{{ $item->category?->name ?? '—' }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-white truncate">{{ $item->name }}</h3>
                            <p class="text-sm text-white/45 mt-1">Tsh {{ number_format($item->price) }}</p>
                            <div class="mt-3">
                                @if($health === 'out')
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                        {{ config('salon.manager_stock_badge_out') }}
                                    </span>
                                @elseif($health === 'low')
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-200 border border-amber-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                        {{ config('salon.manager_stock_badge_low') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-emerald-500/15 text-emerald-300 border border-emerald-500/25">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        {{ config('salon.manager_stock_badge_ok') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <form action="{{ route('manager.stock.update', $item) }}" method="POST" class="flex flex-col sm:flex-row flex-wrap sm:items-end gap-4 lg:justify-end lg:min-w-[min(100%,22rem)]">
                            @csrf
                            @method('PUT')
                            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white/5 border border-white/10 self-start sm:self-end">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-40"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-300/90">{{ config('salon.manager_stock_always_tracked_note') }}</span>
                            </div>
                            <div class="w-full sm:w-auto">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block mb-1">On hand</label>
                                <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', $item->stock_quantity) }}" class="w-full sm:w-28 px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-sm font-mono focus:ring-2 focus:ring-violet-500">
                                <p class="text-[10px] text-white/35 max-w-[14rem] mt-1 leading-snug">{{ config('salon.manager_stock_on_hand_hint') }}</p>
                            </div>
                            <div class="w-full sm:w-auto">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block mb-1">Alert at or below</label>
                                <input type="number" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $item->low_stock_threshold) }}" class="w-full sm:w-28 px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-sm font-mono focus:ring-2 focus:ring-violet-500">
                            </div>
                            <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all self-end">
                                {{ config('salon.manager_stock_save') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="glass-card rounded-2xl p-10 text-center border border-dashed border-white/15">
                <p class="text-white/60 text-sm max-w-md mx-auto">{{ config('salon.manager_stock_empty_hint') }}</p>
            </div>
        @endforelse
    </div>
</x-manager-layout>

