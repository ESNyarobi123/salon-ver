<x-manager-layout>
    <x-slot name="header">
        {{ config('salon.booking_history') }}
    </x-slot>

    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">{{ config('salon.booking_history') }}</h2>
            <p class="text-sm font-medium text-white/40 uppercase tracking-wider">Complete booking records with detailed information</p>
        </div>
        <form method="GET" action="{{ route('manager.orders.history.export') }}">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="hidden" name="waiter" value="{{ $waiter }}">
            <input type="hidden" name="date_from" value="{{ $dateFrom }}">
            <input type="hidden" name="date_to" value="{{ $dateTo }}">
            <button type="submit" class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>
                </svg>
                Export CSV
            </button>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-violet-500/20 to-violet-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Total bookings</p>
                <h3 class="text-3xl font-bold text-white tracking-tight">{{ number_format($totalOrders) }}</h3>
            </div>
        </div>
        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Completed</p>
                <h3 class="text-3xl font-bold text-white tracking-tight">{{ number_format($completedOrders) }}</h3>
            </div>
        </div>
        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-cyan-500/20 to-cyan-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Total Revenue</p>
                <h3 class="text-3xl font-bold text-white tracking-tight">Tsh {{ number_format($totalRevenue) }}</h3>
            </div>
        </div>
        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-amber-500/20 to-amber-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Avg booking value</p>
                <h3 class="text-3xl font-bold text-white tracking-tight">Tsh {{ number_format($avgOrderValue) }}</h3>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card p-6 rounded-2xl mb-8">
        <h3 class="text-lg font-bold text-white mb-4">Filter bookings</h3>
        <form method="GET" action="{{ route('manager.orders.history') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Status</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="preparing" {{ $status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="ready" {{ $status === 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="served" {{ $status === 'served' ? 'selected' : '' }}>Served</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>

            <!-- Stylist filter -->
            <div>
                <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">{{ config('salon.staff') }}</label>
                <select name="waiter" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                    <option value="all" {{ $waiter === 'all' ? 'selected' : '' }}>All {{ config('salon.staff_plural') }}</option>
                    @foreach($waiters as $w)
                        <option value="{{ $w->id }}" {{ $waiter == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent">
            </div>

            <!-- Search -->
            <div>
                <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Search</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="{{ config('salon.seat') }}, client..." class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 font-semibold text-sm text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent">
            </div>

            <!-- Buttons -->
            <div class="md:col-span-2 lg:col-span-5 flex gap-3">
                <button type="submit" class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all">
                    Apply Filters
                </button>
                <a href="{{ route('manager.orders.history') }}" class="glass px-6 py-2.5 rounded-xl font-semibold text-white/70 hover:text-white hover:bg-white/10 transition-all">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Bookings table -->
    <div class="glass-card rounded-2xl overflow-hidden" x-data="{ expandedOrder: null }">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/[0.02]">
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">{{ config('salon.seat') }}</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">{{ config('salon.staff') }}</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($orders as $order)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 bg-violet-500/10 border border-violet-500/20 rounded-lg text-xs font-bold text-violet-400 font-mono tracking-wider">
                                        ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm text-white/60">
                                <div>{{ $order->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-white/40">{{ $order->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-5 font-semibold text-white">{{ config('salon.seat') }} #{{ $order->table_number }}</td>
                            <td class="px-6 py-5 text-sm text-white/70">
                                <div>{{ $order->customer_name ?? 'N/A' }}</div>
                                <div class="text-xs text-white/40">{{ $order->customer_phone ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-5 text-sm text-cyan-400">{{ $order->waiter?->name ?? 'Unassigned' }}</td>
                            <td class="px-6 py-5 text-sm text-white/60">{{ $order->items->count() }} items</td>
                            <td class="px-6 py-5 font-bold text-white">Tsh {{ number_format($order->total_amount) }}</td>
                            <td class="px-6 py-5">
                                @php
                                    $statusColors = [
                                        'pending' => 'rose',
                                        'preparing' => 'amber',
                                        'ready' => 'emerald',
                                        'served' => 'cyan',
                                        'paid' => 'emerald'
                                    ];
                                    $color = $statusColors[$order->status] ?? 'gray';
                                @endphp
                                <span class="bg-{{ $color }}-500/10 text-{{ $color }}-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase border border-{{ $color }}-500/20">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <button @click="expandedOrder = expandedOrder === {{ $order->id }} ? null : {{ $order->id }}" class="text-violet-400 hover:text-violet-300 font-semibold text-sm transition-colors">
                                    <span x-show="expandedOrder !== {{ $order->id }}">View Details</span>
                                    <span x-show="expandedOrder === {{ $order->id }}" x-cloak>Hide Details</span>
                                </button>
                            </td>
                        </tr>
                        <!-- Expanded Details Row -->
                        <tr x-show="expandedOrder === {{ $order->id }}" x-cloak x-transition class="bg-white/[0.02]">
                            <td colspan="9" class="px-6 py-6">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Line items -->
                                    <div class="glass p-5 rounded-xl">
                                        <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">{{ config('salon.services') }} &amp; products</h4>
                                        <div class="space-y-3">
                                            @foreach($order->items as $item)
                                                <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                                                    <div class="flex items-center gap-3">
                                                        @if($item->menuItem && $item->menuItem->image)
                                                            <img src="{{ $item->menuItem->imageUrl() }}" alt="{{ $item->menuItem->name }}" class="w-10 h-10 rounded-lg object-cover">
                                                        @else
                                                            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white/40">
                                                                    <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <p class="font-semibold text-white">{{ $item->menuItem?->name ?? $item->name }}</p>
                                                            <p class="text-xs text-white/40">Qty: {{ $item->quantity }}</p>
                                                        </div>
                                                    </div>
                                                    <p class="font-bold text-white">Tsh {{ number_format($item->price * $item->quantity) }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="mt-4 pt-4 border-t border-white/10 flex justify-between items-center">
                                            <span class="font-bold text-white/60 uppercase text-sm">Total</span>
                                            <span class="text-xl font-bold text-white">Tsh {{ number_format($order->total_amount) }}</span>
                                        </div>
                                    </div>

                                    <!-- Booking details -->
                                    <div class="space-y-4">
                                        <!-- Payment Info -->
                                        @if($order->payments && $order->payments->isNotEmpty())
                                            <div class="glass p-5 rounded-xl">
                                                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Payment Information</h4>
                                                @foreach($order->payments as $payment)
                                                    <div class="space-y-2 text-sm {{ !$loop->last ? 'mb-4 pb-4 border-b border-white/10' : '' }}">
                                                        <div class="flex justify-between">
                                                            <span class="text-white/40">Method:</span>
                                                            <span class="text-white font-semibold uppercase">{{ $payment->method }}</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-white/40">Status:</span>
                                                            <span class="text-emerald-400 font-semibold">{{ ucfirst($payment->status) }}</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-white/40">Amount:</span>
                                                            <span class="text-white font-bold">Tsh {{ number_format($payment->amount) }}</span>
                                                        </div>
                                                        @if($payment->transaction_reference)
                                                            <div class="flex justify-between">
                                                                <span class="text-white/40">Reference:</span>
                                                                <span class="text-white/60 font-mono text-xs">{{ $payment->transaction_reference }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Timeline -->
                                        <div class="glass p-5 rounded-xl">
                                            <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Booking timeline</h4>
                                            <div class="space-y-3">
                                                <div class="flex items-start gap-3">
                                                    <div class="w-2 h-2 bg-violet-500 rounded-full mt-2"></div>
                                                    <div>
                                                        <p class="text-sm font-semibold text-white">Booking created</p>
                                                        <p class="text-xs text-white/40">{{ $order->created_at->format('M d, Y H:i') }}</p>
                                                    </div>
                                                </div>
                                                @if($order->status !== 'pending')
                                                    <div class="flex items-start gap-3">
                                                        <div class="w-2 h-2 bg-amber-500 rounded-full mt-2"></div>
                                                        <div>
                                                            <p class="text-sm font-semibold text-white">Status: {{ ucfirst($order->status) }}</p>
                                                            <p class="text-xs text-white/40">{{ $order->updated_at->format('M d, Y H:i') }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Additional Info -->
                                        <div class="glass p-5 rounded-xl">
                                            <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Additional Information</h4>
                                            <div class="space-y-2 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-white/40">Booking ID:</span>
                                                    <span class="text-violet-400 font-mono font-bold">ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-white/40">{{ config('salon.seat') }}:</span>
                                                    <span class="text-white font-semibold">#{{ $order->table_number }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-white/40">{{ config('salon.staff') }}:</span>
                                                    <span class="text-cyan-400">{{ $order->waiter?->name ?? 'Unassigned' }}</span>
                                                </div>
                                                @if($order->customer_name)
                                                    <div class="flex justify-between">
                                                        <span class="text-white/40">{{ config('salon.customer') }}:</span>
                                                        <span class="text-white">{{ $order->customer_name }}</span>
                                                    </div>
                                                @endif
                                                @if($order->customer_phone)
                                                    <div class="flex justify-between">
                                                        <span class="text-white/40">Phone:</span>
                                                        <span class="text-white">{{ $order->customer_phone }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-20">
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/5">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-white/20">
                                            <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-white mb-2">No bookings found</h3>
                                    <p class="text-sm text-white/40 max-w-md mx-auto">No bookings match your current filters. Try adjusting your search criteria or date range.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-4 border-t border-white/5">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>
</x-manager-layout>
