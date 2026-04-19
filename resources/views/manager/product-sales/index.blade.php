<x-manager-layout>
    <x-slot name="header">
        {{ config('salon.manager_nav_product_sales') }}
    </x-slot>

    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">{{ config('salon.manager_product_sales_title') }}</h2>
            <p class="text-sm font-medium text-white/40 uppercase tracking-wider mt-1">{{ config('salon.manager_product_sales_subtitle') }}</p>
        </div>
    </div>

    {{-- Today (always) --}}
    <p class="text-[10px] font-bold text-white/35 uppercase tracking-[0.2em] mb-3">{{ config('salon.manager_product_sales_today_section') }}</p>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
        <div class="glass-card p-5 rounded-2xl card-hover relative overflow-hidden">
            <div class="absolute -top-8 -right-8 w-24 h-24 bg-emerald-500/15 rounded-full blur-2xl"></div>
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-1">{{ config('salon.manager_product_sales_card_revenue_today') }}</p>
            <h3 class="text-2xl font-bold text-emerald-300 tracking-tight">Tsh {{ number_format($todayStats['revenue'], 0) }}</h3>
        </div>
        <div class="glass-card p-5 rounded-2xl card-hover relative overflow-hidden">
            <div class="absolute -top-8 -right-8 w-24 h-24 bg-violet-500/15 rounded-full blur-2xl"></div>
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-1">{{ config('salon.manager_product_sales_card_sales_today') }}</p>
            <h3 class="text-2xl font-bold text-white tracking-tight">{{ number_format($todayStats['count']) }}</h3>
        </div>
        <div class="glass-card p-5 rounded-2xl card-hover relative overflow-hidden">
            <div class="absolute -top-8 -right-8 w-24 h-24 bg-cyan-500/15 rounded-full blur-2xl"></div>
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-1">{{ config('salon.manager_product_sales_card_units_today') }}</p>
            <h3 class="text-2xl font-bold text-cyan-300 tracking-tight">{{ number_format($todayStats['units']) }}</h3>
        </div>
    </div>

    {{-- Filters --}}
    <div class="glass-card p-6 rounded-2xl mb-8">
        <h3 class="text-lg font-bold text-white mb-4">{{ config('salon.manager_product_sales_filters_title') }}</h3>
        <form method="GET" action="{{ route('manager.product-sales.index') }}" class="space-y-5">
            <div class="flex flex-wrap gap-2">
                @foreach([
                    'today' => config('salon.manager_product_sales_period_today'),
                    '7' => config('salon.manager_product_sales_period_7'),
                    '30' => config('salon.manager_product_sales_period_30'),
                    '90' => config('salon.manager_product_sales_period_90'),
                    'custom' => config('salon.manager_product_sales_period_custom'),
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
                        <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">{{ config('salon.manager_product_sales_date_from') }}</label>
                        <input type="date" name="date_from" value="{{ $dateFromInput }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">{{ config('salon.manager_product_sales_date_to') }}</label>
                        <input type="date" name="date_to" value="{{ $dateToInput }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500">
                    </div>
                </div>
            @else
                <input type="hidden" name="date_from" value="{{ $dateFromInput }}">
                <input type="hidden" name="date_to" value="{{ $dateToInput }}">
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">{{ config('salon.manager_product_sales_filter_status') }}</label>
                    <select name="status" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>{{ config('salon.manager_product_sales_status_all') }}</option>
                        <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>{{ config('salon.manager_product_sales_status_paid') }}</option>
                        <option value="payment_pending" {{ $status === 'payment_pending' ? 'selected' : '' }}>{{ config('salon.manager_product_sales_status_pending') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">{{ config('salon.staff') }}</label>
                    <select name="waiter" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500">
                        <option value="all" {{ $waiterFilter === 'all' || $waiterFilter === '' ? 'selected' : '' }}>{{ config('salon.manager_product_sales_waiter_all') }}</option>
                        @foreach($waiters as $w)
                            <option value="{{ $w->id }}" {{ (string) $waiterFilter === (string) $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all">
                    {{ config('salon.manager_product_sales_apply') }}
                </button>
                <a href="{{ route('manager.product-sales.index') }}" class="glass px-6 py-2.5 rounded-xl font-semibold text-white/70 hover:text-white hover:bg-white/10 transition-all">
                    {{ config('salon.manager_product_sales_clear') }}
                </a>
            </div>
        </form>
    </div>

    <p class="text-[10px] font-bold text-white/35 uppercase tracking-[0.2em] mb-3">
        {{ config('salon.manager_product_sales_period_section') }}
        <span class="text-white/50 font-medium normal-case">({{ $rangeStart->format('M j, Y') }} — {{ $rangeEnd->format('M j, Y') }})</span>
    </p>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">{{ config('salon.manager_product_sales_card_period_revenue') }}</p>
                <h3 class="text-2xl font-bold text-white tracking-tight">Tsh {{ number_format($periodStats['revenue'], 0) }}</h3>
            </div>
        </div>
        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-violet-500/20 to-violet-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">{{ config('salon.manager_product_sales_card_period_sales') }}</p>
                <h3 class="text-2xl font-bold text-white tracking-tight">{{ number_format($periodStats['count']) }}</h3>
            </div>
        </div>
        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-cyan-500/20 to-cyan-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">{{ config('salon.manager_product_sales_card_period_units') }}</p>
                <h3 class="text-2xl font-bold text-white tracking-tight">{{ number_format($periodStats['units']) }}</h3>
            </div>
        </div>
        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-amber-500/20 to-amber-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">{{ config('salon.manager_product_sales_card_pending_push') }}</p>
                <h3 class="text-2xl font-bold text-amber-200 tracking-tight">{{ number_format($pendingInPeriod) }}</h3>
            </div>
        </div>
    </div>

    @if($topProducts->isNotEmpty())
        <div class="glass-card p-6 rounded-2xl mb-8">
            <h3 class="text-lg font-bold text-white mb-4">{{ config('salon.manager_product_sales_top_products') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($topProducts as $row)
                    <div class="flex items-center justify-between bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3">
                        <span class="text-sm font-semibold text-white/90 truncate pr-3">{{ $row->name }}</span>
                        <div class="text-right shrink-0">
                            <span class="text-xs text-white/45 block">{{ number_format($row->units_sold) }} {{ config('salon.manager_product_sales_units_label') }}</span>
                            <span class="text-sm font-bold text-emerald-300">Tsh {{ number_format($row->revenue, 0) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- History table --}}
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h3 class="text-lg font-bold text-white">{{ config('salon.manager_product_sales_history_title') }}</h3>
            <p class="text-xs text-white/40">{{ $sales->total() }} {{ config('salon.manager_product_sales_rows_in_range') }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/[0.02]">
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">{{ config('salon.manager_product_sales_col_time') }}</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">{{ config('salon.seat') }}</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">{{ config('salon.staff') }}</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">{{ config('salon.manager_product_sales_col_items') }}</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">{{ config('salon.manager_product_sales_col_amount') }}</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">{{ config('salon.manager_product_sales_col_status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($sales as $order)
                        @php
                            $lines = $order->items;
                            if ($lines->isEmpty() && is_array($order->pending_line_items)) {
                                $preview = collect($order->pending_line_items)->take(3)->map(fn ($l) => ($l['quantity'] ?? 0).'× '.($l['name'] ?? ''))->filter()->implode(', ');
                                $extra = max(0, count($order->pending_line_items) - 3);
                            } else {
                                $preview = $lines->take(3)->map(fn ($i) => $i->quantity.'× '.$i->name)->implode(', ');
                                $extra = max(0, $lines->count() - 3);
                            }
                        @endphp
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-lime-500/10 border border-lime-500/20 rounded-lg text-xs font-bold text-lime-300 font-mono">#{{ $order->id }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-white/60">
                                <div>{{ $order->created_at->timezone(config('app.timezone'))->format('M d, Y') }}</div>
                                <div class="text-xs text-white/35">{{ $order->created_at->timezone(config('app.timezone'))->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-white">{{ $order->table_number ?: '—' }}</td>
                            <td class="px-6 py-4 text-sm text-white/70">{{ $order->waiter?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-white/60 max-w-xs">
                                <span class="line-clamp-2">{{ $preview ?: '—' }}@if($extra > 0) <span class="text-white/35">+{{ $extra }}</span>@endif</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-300">Tsh {{ number_format($order->total_amount, 0) }}</td>
                            <td class="px-6 py-4">
                                @if($order->status === 'paid')
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-lg bg-emerald-500/15 text-emerald-300 border border-emerald-500/25">{{ config('salon.manager_product_sales_status_paid') }}</span>
                                @else
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-lg bg-amber-500/15 text-amber-200 border border-amber-500/25">{{ config('salon.manager_product_sales_status_pending') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-white/40">{{ config('salon.manager_product_sales_empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
            <div class="px-6 py-4 border-t border-white/5">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</x-manager-layout>
