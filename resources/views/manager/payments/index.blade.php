<x-manager-layout>
    <x-slot name="header">
        Payments & Revenue
    </x-slot>

    <style>
        /* Revenue Chart Modern Styling */
        .revenue-chart-container {
            background: linear-gradient(135deg, rgba(17, 24, 39, 0.8) 0%, rgba(30, 27, 75, 0.6) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }
        .chart-bar {
            background: linear-gradient(to top, #3b82f6 0%, #06b6d4 50%, #8b5cf6 100%);
            border-radius: 6px 6px 0 0;
            box-shadow: 0 -4px 20px rgba(139, 92, 246, 0.5);
            width: 100%;
            transform-origin: bottom;
        }
        .chart-bar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 40%;
            background: linear-gradient(180deg, rgba(255,255,255,0.3) 0%, transparent 100%);
            pointer-events: none;
        }
        .chart-bar:hover {
            filter: brightness(1.2);
            box-shadow: 0 8px 30px rgba(139, 92, 246, 0.6), 0 0 50px rgba(139, 92, 246, 0.3);
        }
        .chart-bar.animate {
            animation: barGrow 0.8s ease-out forwards;
            transform-origin: bottom;
        }
        @keyframes barGrow {
            from { 
                opacity: 0;
                transform: scaleY(0);
            }
            to { 
                opacity: 1;
                transform: scaleY(1);
            }
        }
        .chart-tooltip {
            background: rgba(0, 0, 0, 0.9);
            border: 1px solid rgba(139, 92, 246, 0.5);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 40px rgba(139, 92, 246, 0.3);
            position: relative;
        }
        .stat-comparison {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(6, 182, 212, 0.1) 100%);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .stat-comparison.negative {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(245, 158, 11, 0.1) 100%);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
    </style>

    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">Payments & Revenue</h2>
            <p class="text-sm font-medium text-white/40 uppercase tracking-wider">Track your earnings and payment methods</p>
        </div>
        <div class="flex gap-3">
            <form method="GET" action="{{ route('manager.payments.export') }}" class="inline">
                <input type="hidden" name="period" value="{{ $period }}">
                @if($period === 'custom')
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">
                @endif
                <button type="submit" class="glass px-5 py-3 rounded-xl font-semibold text-white/70 hover:text-white hover:bg-white/10 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>
                    </svg>
                    Export CSV
                </button>
            </form>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="glass-card p-6 rounded-2xl mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-white">Filter Payments</h3>
                <p class="text-xs text-white/40">Showing {{ ucfirst($period) }} payments</p>
            </div>
            @if($payments->isEmpty() && $totalRevenue == 0)
                <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl px-4 py-2">
                    <p class="text-xs font-bold text-amber-400">💡 No payments found for this period</p>
                </div>
            @endif
        </div>
        <form method="GET" action="{{ route('manager.payments.index') }}" class="flex flex-wrap items-end gap-4" x-data="{ period: '{{ $period }}' }">
            <div>
                <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Period</label>
                <select name="period" x-model="period" class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            <div x-show="period === 'custom'" x-cloak>
                <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent">
            </div>
            <div x-show="period === 'custom'" x-cloak>
                <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent">
            </div>
            <button type="submit" class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all">
                Apply Filter
            </button>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass-card p-6 rounded-2xl">
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Total Revenue</p>
            <p class="text-3xl font-bold text-white mb-1">Tsh {{ number_format($totalRevenue) }}</p>
            <p class="text-xs text-emerald-400 font-semibold">{{ $totalOrders }} orders</p>
        </div>
        <div class="glass-card p-6 rounded-2xl">
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Total Tips</p>
            <p class="text-3xl font-bold text-white mb-1">Tsh {{ number_format($totalTips) }}</p>
            <p class="text-xs text-cyan-400 font-semibold">{{ $tips->count() }} tips</p>
        </div>
        <div class="glass-card p-6 rounded-2xl">
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Avg booking value</p>
            <p class="text-3xl font-bold text-white mb-1">Tsh {{ number_format($avgOrderValue) }}</p>
            <p class="text-xs text-violet-400 font-semibold">Per transaction</p>
        </div>
        <div class="glass-card p-6 rounded-2xl">
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Total Earnings</p>
            <p class="text-3xl font-bold text-white mb-1">Tsh {{ number_format($totalRevenue + $totalTips) }}</p>
            <p class="text-xs text-amber-400 font-semibold">Revenue + Tips</p>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="revenue-chart-container glass-card p-8 rounded-2xl mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-white tracking-tight">
                    Revenue Trend
                    @if($period === 'today')
                        <span class="text-violet-400">(Today)</span>
                    @elseif($period === 'week')
                        <span class="text-violet-400">(This Week)</span>
                    @elseif($period === 'month')
                        <span class="text-violet-400">(This Month)</span>
                    @elseif($period === 'custom' && $startDate && $endDate)
                        <span class="text-violet-400">({{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d') }})</span>
                    @else
                        <span class="text-violet-400">(Last 7 Days)</span>
                    @endif
                </h3>
                <p class="text-xs text-white/40 mt-1">Daily revenue breakdown with trend analysis</p>
            </div>
            
            <!-- Comparison Badge -->
            @php
                $totalCurrent = collect($dailyRevenue)->sum('revenue');
                $avgDaily = count($dailyRevenue) > 0 ? $totalCurrent / count($dailyRevenue) : 0;
            @endphp
            @if($totalCurrent > 0)
                <div class="stat-comparison px-4 py-2 rounded-xl flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-400">
                        <path d="M12 2v20M2 12h20M17 7l-5-5-5 5M7 17l5 5 5-5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-sm font-bold text-emerald-400">Avg Tsh {{ number_format($avgDaily) }}/day</span>
                </div>
            @endif
        </div>
        
        <div class="h-72 px-4 pb-4 border-b border-white/10">
            <div class="h-full flex items-end justify-between gap-2">
                @forelse($dailyRevenue as $day)
                    @php
                        $maxRevenue = collect($dailyRevenue)->max('revenue') ?: 1;
                        $heightPercent = ($day['revenue'] / $maxRevenue) * 100;
                        $heightPercent = max($heightPercent, 2);
                    @endphp
                    <div class="flex-1 h-full flex flex-col justify-end items-center group" style="min-width: 30px;">
                        <!-- Bar -->
                        <div class="w-full relative group-hover:brightness-110 transition-all"
                             style="height: {{ $heightPercent }}%; 
                                    background: linear-gradient(to top, #3b82f6, #06b6d4, #8b5cf6);
                                    border-radius: 6px 6px 0 0;
                                    box-shadow: 0 -4px 15px rgba(139, 92, 246, 0.4);
                                    min-height: 2px;">
                            <!-- Tooltip -->
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 rounded bg-black/90 border border-violet-500/50 text-white text-xs whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                                Tsh {{ number_format($day['revenue']) }}
                            </div>
                            <!-- Shine -->
                            <div class="absolute top-0 left-0 right-0 h-1/3 bg-gradient-to-b from-white/30 to-transparent rounded-t pointer-events-none"></div>
                        </div>
                        <!-- Label -->
                        <div class="mt-2 text-center">
                            <p class="text-[10px] font-bold text-white/70">{{ $day['date'] }}</p>
                            <p class="text-[9px] {{ $day['revenue'] > 0 ? 'text-violet-400' : 'text-white/30' }}">
                                {{ $day['revenue'] > 0 ? number_format($day['revenue']/1000,1).'k' : '0' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="w-full h-full flex items-center justify-center text-white/40">
                        No revenue data
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Chart Legend & Stats -->
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-white/5">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-gradient-to-r from-violet-500 to-cyan-500"></div>
                    <span class="text-xs text-white/60">Revenue</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="text-xs text-white/60">Growth</span>
                </div>
            </div>
            <div class="flex items-center gap-6 text-right">
                <div>
                    <p class="text-[10px] text-white/40 uppercase tracking-wider">Total</p>
                    <p class="text-lg font-bold text-white">Tsh {{ number_format($totalCurrent) }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-white/40 uppercase tracking-wider">Highest</p>
                    <p class="text-lg font-bold text-violet-400">Tsh {{ number_format(collect($dailyRevenue)->max('revenue')) }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-white/40 uppercase tracking-wider">Days</p>
                    <p class="text-lg font-bold text-cyan-400">{{ count($dailyRevenue) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card p-6 rounded-2xl flex items-center gap-5 card-hover">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-xl flex items-center justify-center border border-emerald-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-400">
                    <rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-white/40 uppercase tracking-wider">Cash Payments</p>
                <p class="text-xl font-bold text-white">Tsh {{ number_format($cashRevenue) }}</p>
            </div>
        </div>
        <div class="glass-card p-6 rounded-2xl flex items-center gap-5 card-hover">
            <div class="w-12 h-12 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center border border-cyan-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400">
                    <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-white/40 uppercase tracking-wider">USSD / Mobile</p>
                <p class="text-xl font-bold text-white">Tsh {{ number_format($ussdRevenue + $mobileRevenue) }}</p>
            </div>
        </div>
        <div class="glass-card p-6 rounded-2xl flex items-center gap-5 card-hover">
            <div class="w-12 h-12 bg-gradient-to-br from-violet-500/20 to-purple-500/20 rounded-xl flex items-center justify-center border border-violet-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet-400">
                    <rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-white/40 uppercase tracking-wider">Tips Collected</p>
                <p class="text-xl font-bold text-white">Tsh {{ number_format($totalTips) }}</p>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-white/5">
            <h3 class="text-xl font-bold text-white tracking-tight">Recent Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/[0.02]">
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">{{ config('salon.staff') }}</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-5 text-sm text-white/60">{{ $payment->created_at->format('M d, H:i') }}</td>
                            <td class="px-6 py-5 font-semibold text-white">{{ config('salon.seat') }} #{{ $payment->order?->table_number ?? 'N/A' }}</td>
                            <td class="px-6 py-5 text-sm text-cyan-400">{{ $payment->waiter?->name ?? 'Unassigned' }}</td>
                            <td class="px-6 py-5">
                                <span class="bg-cyan-500/10 text-cyan-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase border border-cyan-500/20">{{ $payment->method }}</span>
                            </td>
                            <td class="px-6 py-5 font-bold text-white">Tsh {{ number_format($payment->amount) }}</td>
                            <td class="px-6 py-5">
                                <span class="text-emerald-400 font-semibold flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20">
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/5">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-white/20">
                                            <rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-white mb-2">No Transactions Yet</h3>
                                    <p class="text-sm text-white/40 max-w-md mx-auto">No payment transactions found for the selected period. Try selecting a different date range or check back after completing some orders.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-white/5">
            {{ $payments->links() }}
        </div>
    </div>
</x-manager-layout>
