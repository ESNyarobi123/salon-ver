<x-admin-layout>
    <x-slot name="header">Booking management</x-slot>

    <div class="glass-card rounded-2xl overflow-hidden border border-white/10">
        <div class="p-6 border-b border-white/5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight">All system bookings</h2>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Monitor and manage bookings across all {{ config('salon.entity_plural_lower') }}</p>
                </div>
                <a href="{{ route('admin.orders.export', request()->query()) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/10 transition-all shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export CSV
                </a>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.orders.index') }}" class="mt-6 flex flex-wrap items-end gap-4">
                <div class="relative flex-1 min-w-[180px] max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-1 block">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="ID, customer, phone..."
                           class="w-full pl-10 pr-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-sm font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="absolute left-3 top-[38px] text-white/40"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                <div class="min-w-[140px]">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-1 block">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-sm font-medium text-white focus:ring-2 focus:ring-violet-500 [&>option]:bg-gray-900">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="min-w-[180px]">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-1 block">{{ config('salon.entity') }}</label>
                    <select name="restaurant_id" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-sm font-medium text-white focus:ring-2 focus:ring-violet-500 [&>option]:bg-gray-900">
                        <option value="">All {{ config('salon.entity_plural_lower') }}</option>
                        @foreach($restaurants as $r)
                            <option value="{{ $r->id }}" {{ request('restaurant_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[130px]">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-1 block">From date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-sm font-medium text-white focus:ring-2 focus:ring-violet-500">
                </div>
                <div class="min-w-[130px]">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-1 block">To date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-sm font-medium text-white focus:ring-2 focus:ring-violet-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-violet-600 hover:bg-violet-500 text-white rounded-xl font-semibold text-sm transition-all flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/15 text-white rounded-xl font-semibold text-sm border border-white/10 transition-all">Clear</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[700px]">
                <thead>
                    <tr class="bg-white/5">
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Booking ID</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">{{ config('salon.entity') }}</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Amount</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-white/40 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($orders as $order)
                    <tr class="hover:bg-white/5 transition-all">
                        <td class="px-6 py-5">
                            <span class="font-black text-white">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm text-white font-bold">{{ $order->restaurant?->name ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm text-white font-black">Tsh {{ number_format($order->total_amount, 0) }}</span>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $statusColor = match($order->status) {
                                    'pending' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                                    'preparing' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                    'ready' => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                                    'completed' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                    'cancelled' => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
                                    default => 'bg-white/10 text-white/60 border-white/20',
                                };
                            @endphp
                            <span class="px-3 py-1.5 rounded-full {{ $statusColor }} text-[10px] font-black uppercase tracking-widest border">{{ $order->status }}</span>
                        </td>
                        <td class="px-6 py-5 text-sm text-white/60 font-medium">{{ $order->created_at->format('M d, H:i') }}</td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.orders.show', $order) }}" class="p-2 glass text-white/40 hover:bg-violet-600 hover:text-white rounded-xl transition-all" title="View"><i data-lucide="eye" class="w-4 h-4"></i></a>
                                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('Delete this booking?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 glass text-rose-400 hover:bg-rose-500 hover:text-white rounded-xl transition-all" title="Delete"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/30"><i data-lucide="package" class="w-8 h-8"></i></div>
                                <p class="text-white font-bold">No bookings found</p>
                                <p class="text-sm text-white/50">Try changing filters or date range.</p>
                                <a href="{{ route('admin.orders.index') }}" class="text-violet-400 hover:text-violet-300 text-sm font-semibold">Clear filters</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="p-6 border-t border-white/5">{{ $orders->links() }}</div>
        @endif
    </div>
</x-admin-layout>
