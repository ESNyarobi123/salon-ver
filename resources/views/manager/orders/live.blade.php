<x-manager-layout>
    <x-slot name="header">
        {{ config('salon.live_bookings') }}
    </x-slot>

    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">{{ config('salon.live_bookings') }}</h2>
            <p class="text-sm font-medium text-white/40 uppercase tracking-wider">Real-time board</p>
            <p class="text-[13px] text-white/50 mt-1 max-w-xl leading-relaxed">Cards highlight <span class="text-violet-200/90">appointment date &amp; time</span>, guest, phone, stylist, and services—scannable at a glance.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="openCreateOrderModal()" class="bg-violet-600 hover:bg-violet-700 text-white px-5 py-3 rounded-xl font-semibold transition-all flex items-center gap-2 shadow-lg shadow-violet-600/20">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                New booking
            </button>
            <div class="flex items-center gap-2 glass px-4 py-2.5 rounded-xl">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Live Sync Active</span>
            </div>
            <button onclick="window.location.reload()" class="glass px-5 py-3 rounded-xl font-semibold text-white/70 hover:text-white hover:bg-white/10 transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Live Kanban Board -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Pending -->
        <div class="glass-card p-5 rounded-2xl min-h-[500px]">
            <div class="flex items-center justify-between mb-5 px-1">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></div>
                    <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Pending</h4>
                </div>
                <span class="bg-rose-500/20 text-rose-400 text-[11px] font-bold px-2.5 py-1 rounded-full border border-rose-500/20">{{ $pendingOrders->count() }}</span>
            </div>
            <div class="space-y-3">
                @forelse($pendingOrders as $order)
                    <div class="glass p-4 rounded-xl card-hover group border border-white/[0.06] hover:border-rose-500/20 transition-colors">
                        @include('manager.orders.partials.booking-card-summary', ['order' => $order, 'tone' => 'rose'])
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
                                <span class="block text-[9px] font-bold uppercase tracking-wider text-white/35">Total</span>
                                <span class="text-base font-bold text-white tabular-nums">Tsh {{ number_format($order->total_amount) }}</span>
                            </div>
                            <div class="flex gap-2">
                                <form action="{{ route('manager.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Delete this booking?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg hover:bg-white/10 text-white/40 hover:text-rose-400 transition-all" title="Delete booking">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                                <button type="button" onclick="openEditOrderModal(@js($order->id), @js($order->table_number), @js($order->customer_phone ?? ''), @js($order->customer_name ?? ''), @js($order->waiter_id), @js(optional($order->scheduled_at)?->format('Y-m-d')), @js(optional($order->scheduled_at)?->format('H:i')))" class="p-2 rounded-lg hover:bg-white/10 text-white/40 hover:text-violet-400 transition-all" title="Edit booking">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>
                                <form action="{{ route('manager.orders.update', $order->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="preparing">
                                    <button type="submit" class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white p-2 rounded-lg hover:shadow-lg hover:shadow-violet-500/25 transition-all" title="Start Preparing">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="5 3 19 12 5 21 5 3"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-white/30 text-center py-8">No pending bookings</p>
                @endforelse
            </div>
        </div>

        <!-- Preparing -->
        <div class="glass-card p-5 rounded-2xl min-h-[500px]">
            <div class="flex items-center justify-between mb-5 px-1">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></div>
                    <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">{{ config('salon.order_column_in_progress') }}</h4>
                </div>
                <span class="bg-amber-500/20 text-amber-400 text-[11px] font-bold px-2.5 py-1 rounded-full border border-amber-500/20">{{ $preparingOrders->count() }}</span>
            </div>
            <div class="space-y-3">
                @forelse($preparingOrders as $order)
                    <div class="glass p-4 rounded-xl card-hover border border-white/[0.06] hover:border-amber-500/25 transition-colors">
                        @include('manager.orders.partials.booking-card-summary', ['order' => $order, 'tone' => 'amber'])
                        <div class="mt-3 mb-4 rounded-xl border border-white/5 bg-black/25 overflow-hidden divide-y divide-white/[0.06]">
                            @foreach($order->items as $item)
                                <div class="flex justify-between gap-2 items-center text-sm px-3 py-2.5 hover:bg-white/[0.03]">
                                    <span class="font-medium text-white/95 min-w-0 truncate">{{ $item->quantity }}× {{ $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Custom item') }}</span>
                                    <span class="text-white/45 tabular-nums shrink-0 text-xs">Tsh {{ number_format($item->total) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-white/10 gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="w-1.5 h-1.5 {{ $order->status === 'ready' ? 'bg-emerald-400' : 'bg-amber-400' }} rounded-full {{ $order->status === 'ready' ? '' : 'animate-ping' }}"></span>
                                    <span class="text-[10px] font-bold {{ $order->status === 'ready' ? 'text-emerald-400' : 'text-amber-400' }} uppercase tracking-wider">{{ $order->status === 'ready' ? 'Ready (floor)' : 'In progress' }}</span>
                                </div>
                                <span class="block text-[9px] font-bold uppercase tracking-wider text-white/35">Total</span>
                                <span class="text-sm font-bold text-white tabular-nums">Tsh {{ number_format($order->total_amount) }}</span>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <form action="{{ route('manager.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Delete this booking?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg hover:bg-white/10 text-white/40 hover:text-rose-400 transition-all" title="Delete booking">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                                <button type="button" onclick="openEditOrderModal(@js($order->id), @js($order->table_number), @js($order->customer_phone ?? ''), @js($order->customer_name ?? ''), @js($order->waiter_id), @js(optional($order->scheduled_at)?->format('Y-m-d')), @js(optional($order->scheduled_at)?->format('H:i')))" class="p-2 rounded-lg hover:bg-white/10 text-white/40 hover:text-violet-400 transition-all" title="Edit booking">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>
                                <form action="{{ route('manager.orders.update', $order->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="served">
                                    <button type="submit" class="bg-emerald-500 text-white p-2 rounded-lg hover:bg-emerald-600 transition-all" title="Mark as Served">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-white/30 text-center py-8">Nothing on {{ strtolower(config('salon.floor_display_short')) }} yet</p>
                @endforelse
            </div>
        </div>

        <!-- Ready / Served -->
        <div class="glass-card p-5 rounded-2xl min-h-[500px]">
            <div class="flex items-center justify-between mb-5 px-1">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                    <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Served</h4>
                </div>
                <span class="bg-emerald-500/20 text-emerald-400 text-[11px] font-bold px-2.5 py-1 rounded-full border border-emerald-500/20">{{ $servedOrders->count() }}</span>
            </div>
            <div class="space-y-3">
                @forelse($servedOrders as $order)
                    <div class="glass p-4 rounded-xl card-hover border border-white/[0.06] hover:border-emerald-500/25 transition-colors">
                        @include('manager.orders.partials.booking-card-summary', ['order' => $order, 'tone' => 'emerald'])
                        <div class="mt-3 mb-4 rounded-xl border border-white/5 bg-black/25 overflow-hidden divide-y divide-white/[0.06]">
                            @foreach($order->items as $item)
                                <div class="flex justify-between gap-2 items-center text-sm px-3 py-2.5 hover:bg-white/[0.03]">
                                    <span class="font-medium text-white/95 min-w-0 truncate">{{ $item->quantity }}× {{ $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Custom item') }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between gap-2 mb-3 pt-2 border-t border-white/10">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-white/35">Total</span>
                            <span class="text-sm font-bold text-white tabular-nums">Tsh {{ number_format($order->total_amount) }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="openPaymentModal({{ $order->id }}, {{ $order->total_amount }})"
                                    class="flex-1 min-w-[120px] bg-gradient-to-r from-violet-600 to-cyan-600 text-white py-2.5 rounded-xl font-semibold text-sm hover:shadow-lg hover:shadow-violet-500/25 transition-all">
                                Process Payment
                            </button>
                            <form action="{{ route('manager.orders.update', $order) }}" method="POST" class="inline" onsubmit="return confirm('Confirm customer has paid (e.g. via WhatsApp/cash)?');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="paid">
                                <button type="submit" class="py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm border border-emerald-500/30 transition-all" title="{{ config('salon.customer') }} paid outside (WhatsApp/cash)">
                                    Confirm paid
                                </button>
                            </form>
                                <form action="{{ route('manager.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Delete this booking?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-full px-3 rounded-xl hover:bg-white/10 text-white/40 hover:text-rose-400 transition-all" title="Delete booking">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                                <button type="button" onclick="openEditOrderModal(@js($order->id), @js($order->table_number), @js($order->customer_phone ?? ''), @js($order->customer_name ?? ''), @js($order->waiter_id), @js(optional($order->scheduled_at)?->format('Y-m-d')), @js(optional($order->scheduled_at)?->format('H:i')))" class="h-full px-3 rounded-xl hover:bg-white/10 text-white/40 hover:text-violet-400 transition-all" title="Edit booking">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>
                            </div>
                    </div>
                @empty
                    <p class="text-sm text-white/30 text-center py-8">No served bookings</p>
                @endforelse
            </div>
        </div>

        <!-- Completed -->
        <div class="glass-card p-5 rounded-2xl min-h-[500px]">
            <div class="flex items-center justify-between mb-5 px-1">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-cyan-500 rounded-full"></div>
                    <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Completed</h4>
                </div>
                <span class="bg-cyan-500/20 text-cyan-400 text-[11px] font-bold px-2.5 py-1 rounded-full border border-cyan-500/20">{{ $paidOrders->count() }}</span>
            </div>
            <div class="space-y-3 opacity-[0.82]">
                @forelse($paidOrders as $order)
                    <div class="glass p-4 rounded-xl border border-white/[0.05]">
                        <div class="flex justify-between items-start gap-2 mb-2">
                            <div class="min-w-0 flex-1">
                                @include('manager.orders.partials.booking-card-summary', ['order' => $order, 'tone' => 'cyan', 'compact' => true])
                            </div>
                            <div class="flex items-center gap-2 shrink-0 pt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-400">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                <form action="{{ route('manager.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Delete this booking?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white/40 hover:text-rose-400 transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4c1 0 2 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-white/10 mt-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400/80">Paid</span>
                            <span class="text-sm font-bold text-white/90 tabular-nums">Tsh {{ number_format($order->total_amount) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-white/30 text-center py-8">No completed bookings today</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Create booking modal -->
    <div id="createOrderModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
        <div
            class="bg-surface-900 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden border border-white/10 max-h-[90vh] flex flex-col"
            x-data="bookingWizard(@js($bookingCategories->isEmpty()))"
        >
            <div class="p-6 border-b border-white/10 flex justify-between items-center gap-4">
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight">New booking</h3>
                    <p class="text-[11px] text-white/45 mt-1">Three quick steps: when → who &amp; where → services</p>
                </div>
                <button type="button" onclick="closeCreateOrderModal()" class="p-2 hover:bg-white/10 rounded-xl transition-all text-white/40 hover:text-white shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form action="{{ route('manager.orders.store') }}" method="POST" class="flex-1 flex flex-col min-h-0">
                @csrf
                <div class="px-6 pt-5 pb-2">
                    <div class="flex gap-2">
                        @foreach ([1 => 'When', 2 => 'Details', 3 => 'Services'] as $n => $label)
                            <div class="flex-1 rounded-xl px-3 py-2.5 text-center border transition-all"
                                 :class="step === {{ $n }} ? 'border-violet-500/50 bg-violet-500/15 text-white' : (step > {{ $n }} ? 'border-emerald-500/25 bg-emerald-500/10 text-emerald-200/90' : 'border-white/10 bg-white/5 text-white/40')">
                                <span class="block text-[9px] font-bold uppercase tracking-wider opacity-70">Step {{ $n }}</span>
                                <span class="text-xs font-semibold">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5 custom-scrollbar">
                    @if ($errors->any())
                        <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-100 space-y-1">
                            @foreach ($errors->all() as $err)
                                <p>{{ $err }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div x-show="step === 1" class="space-y-5">
                        <div class="rounded-2xl border border-violet-500/20 bg-gradient-to-br from-violet-600/20 to-cyan-600/10 p-5">
                            <p class="text-sm font-semibold text-white mb-1">When should we expect them?</p>
                            <p class="text-[11px] text-white/50 leading-relaxed">Set the slot the guest asked for. You can always edit it later from the board.</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Appointment date</label>
                                <input type="date" name="scheduled_date" required value="{{ old('scheduled_date', now()->format('Y-m-d')) }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all [color-scheme:dark]">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Appointment time</label>
                                <input type="time" name="scheduled_time" required value="{{ old('scheduled_time', now()->format('H:i')) }}" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all [color-scheme:dark]">
                            </div>
                        </div>
                    </div>

                    <div x-show="step === 2" class="space-y-4">
                        <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-5 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.seat') }}</label>
                                    <select name="table_number" required class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all [&>option]:text-black">
                                        <option value="">Select {{ config('salon.seat') }}</option>
                                        @foreach($tables as $table)
                                            <option value="{{ $table->name }}" @selected(old('table_number') === $table->name)>{{ $table->name }} ({{ $table->capacity }} pax)</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} name <span class="text-white/25 font-normal">(optional)</span></label>
                                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="{{ config('salon.customer') }} name" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} phone <span class="text-white/25 font-normal">(optional)</span></label>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="07XXXXXXXX" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Assign {{ strtolower(config('salon.staff')) }} <span class="text-white/25 font-normal">(optional)</span></label>
                                <select name="waiter_id" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all [&>option]:text-black">
                                    <option value="">— Unassigned —</option>
                                    @foreach($waiters as $w)
                                        <option value="{{ $w->id }}" @selected((string) old('waiter_id') === (string) $w->id)>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                                @if($waiters->isEmpty())
                                    <p class="text-[10px] text-amber-400/90 mt-2">No team members with the stylist role yet. Add staff under Team, or leave unassigned.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div x-show="step === 3" class="space-y-4">
                        <div class="flex items-start justify-between gap-3 rounded-xl border border-white/10 bg-white/[0.02] px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold text-white">{{ config('salon.services') }} <span class="text-white/40 font-normal">by category</span></p>
                                <p class="text-[11px] text-white/45 mt-0.5">Only <span class="text-violet-200/90">service</span> catalog items appear here. Tick each line and set quantity.</p>
                            </div>
                        </div>
                        @if($bookingCategories->isEmpty())
                            <div class="rounded-2xl border border-amber-500/25 bg-amber-500/10 px-5 py-6 text-center">
                                <p class="text-sm font-semibold text-amber-100">No bookable services yet</p>
                                <p class="text-[12px] text-amber-100/75 mt-2 leading-relaxed">Add menu items under a <strong class="text-white">Service</strong> category in your catalog, mark them available, then refresh this page.</p>
                            </div>
                        @else
                            <div class="space-y-5 max-h-[min(42vh,22rem)] overflow-y-auto pr-1 custom-scrollbar">
                                @php $itemFormIndex = 0 @endphp
                                @foreach($bookingCategories as $category)
                                    <div class="rounded-xl border border-white/10 overflow-hidden bg-black/20">
                                        <div class="px-4 py-2.5 bg-white/5 border-b border-white/10">
                                            <span class="text-[11px] font-bold uppercase tracking-wider text-violet-200/90">{{ $category->name }}</span>
                                        </div>
                                        <div class="p-3 space-y-2">
                                            @foreach($category->menuItems as $item)
                                                <div class="flex items-center justify-between glass p-3 rounded-xl">
                                                    <div class="flex items-center gap-3 min-w-0">
                                                        <input type="checkbox" id="item_{{ $item->id }}" name="items[{{ $itemFormIndex }}][id]" value="{{ $item->id }}" class="w-5 h-5 shrink-0 rounded border-white/20 bg-white/5 text-violet-600 focus:ring-violet-500 focus:ring-offset-0" onchange="toggleQuantity({{ $itemFormIndex }})">
                                                        <label for="item_{{ $item->id }}" class="text-sm font-medium text-white cursor-pointer select-none truncate">
                                                            {{ $item->name }}
                                                            <span class="block text-[10px] text-white/40">Tsh {{ number_format($item->price) }}</span>
                                                        </label>
                                                    </div>
                                                    <div class="flex items-center gap-2 opacity-50 transition-all shrink-0" id="qty_container_{{ $itemFormIndex }}">
                                                        <button type="button" id="qty_minus_{{ $itemFormIndex }}" onclick="adjustQty({{ $itemFormIndex }}, -1)" disabled class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white hover:bg-white/20 disabled:opacity-30 disabled:pointer-events-none">-</button>
                                                        <input type="number" name="items[{{ $itemFormIndex }}][quantity]" id="qty_{{ $itemFormIndex }}" value="1" min="1" disabled class="w-12 text-center bg-transparent border-none text-white font-bold focus:ring-0 p-0 disabled:opacity-50" readonly>
                                                        <button type="button" id="qty_plus_{{ $itemFormIndex }}" onclick="adjustQty({{ $itemFormIndex }}, 1)" disabled class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white hover:bg-white/20 disabled:opacity-30 disabled:pointer-events-none">+</button>
                                                    </div>
                                                </div>
                                                @php $itemFormIndex++ @endphp
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-6 pt-2 border-t border-white/10 flex flex-col sm:flex-row gap-3 shrink-0">
                    <button type="button" onclick="closeCreateOrderModal()" class="w-full sm:w-auto px-5 py-3 rounded-xl font-semibold text-white/60 hover:text-white hover:bg-white/10 transition-all order-last sm:order-first">
                        Cancel
                    </button>
                    <div class="flex-1 flex gap-3">
                        <button type="button" x-show="step > 1" x-on:click="prev()" class="flex-1 py-3.5 rounded-xl font-bold border border-white/15 text-white/80 hover:bg-white/10 transition-all">
                            Back
                        </button>
                        <button type="button" x-show="step < 3" x-on:click="next()" class="flex-1 bg-white/10 hover:bg-white/15 text-white py-3.5 rounded-xl font-bold border border-white/10 transition-all">
                            Continue
                        </button>
                        <button type="submit" x-show="step === 3" class="flex-1 bg-gradient-to-r from-violet-600 to-cyan-600 text-white py-3.5 rounded-xl font-bold hover:shadow-lg hover:shadow-violet-500/25 transition-all">
                            Create booking
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div id="editOrderModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
        <div class="bg-surface-900 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden border border-white/10">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight">Edit booking</h3>
                    <p class="text-[11px] text-white/40 mt-1">Clear both appointment fields to remove the slot from the card.</p>
                </div>
                <button type="button" onclick="closeEditOrderModal()" class="p-2 hover:bg-white/10 rounded-xl transition-all text-white/40 hover:text-white shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <form id="editOrderForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.seat') }}</label>
                    <select name="table_number" id="edit_table_number" required class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 transition-all [&>option]:text-black">
                        @foreach($tables as $table)
                            <option value="{{ $table->name }}">{{ $table->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} name</label>
                    <input type="text" name="customer_name" id="edit_customer_name" placeholder="{{ config('salon.customer') }} name" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 transition-all">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} phone</label>
                    <input type="text" name="customer_phone" id="edit_customer_phone" placeholder="07XXXXXXXX" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 transition-all">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Appointment date</label>
                        <input type="date" name="scheduled_date" id="edit_scheduled_date" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 transition-all [color-scheme:dark]">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Appointment time</label>
                        <input type="time" name="scheduled_time" id="edit_scheduled_time" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 transition-all [color-scheme:dark]">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.staff') }}</label>
                    <select name="waiter_id" id="edit_waiter_id" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 transition-all [&>option]:text-black">
                        <option value="">— Unassigned —</option>
                        @foreach($waiters as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-violet-600 text-white py-3.5 rounded-xl font-bold hover:bg-violet-700 transition-all">
                        Update details
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
        <div class="bg-surface-900 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden border border-white/10">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white tracking-tight">Process Payment</h3>
                        <p class="text-sm font-medium text-white/40">Selcom USSD Push</p>
                    </div>
                    <button onclick="closePaymentModal()" class="p-2 hover:bg-white/10 rounded-xl transition-all text-white/40 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="glass p-5 rounded-xl mb-6 flex justify-between items-center">
                    <span class="font-medium text-white/60">Total Amount</span>
                    <span id="modalAmount" class="text-2xl font-bold text-white">Tsh 0</span>
                </div>

                <form id="selcomPayForm" class="space-y-4">
                    <input type="hidden" id="modalOrderId">
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} phone (07XXXXXXXX)</label>
                        <input type="text" id="customerPhone" required placeholder="e.g. 0744963858" 
                               class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} name</label>
                        <input type="text" id="customerName" required placeholder="e.g. John Doe" 
                               class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    </div>
                    <button type="submit" id="payButton" class="w-full bg-gradient-to-r from-violet-600 to-cyan-600 text-white py-3.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/>
                        </svg>
                        Send USSD Push
                    </button>
                </form>

                <div id="pollingStatus" class="hidden mt-6 p-5 bg-cyan-500/10 rounded-xl border border-cyan-500/20 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-8 h-8 border-3 border-cyan-400 border-t-transparent rounded-full animate-spin"></div>
                        <p class="text-sm font-semibold text-cyan-400">Waiting for customer to enter PIN...</p>
                        <p class="text-[10px] text-white/40 font-medium uppercase tracking-wider">Do not close this window</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        /**
         * Live booking wizard — keep logic in a plain function so `this.$el` quirks
         * never block Continue; query the modal root by id instead.
         */
        function bookingWizard(servicesEmpty) {
            const empty = servicesEmpty === true || servicesEmpty === 'true' || servicesEmpty === 1;
            return {
                step: 1,
                maxStep: 3,
                modalRoot() {
                    return document.getElementById('createOrderModal');
                },
                next() {
                    const root = this.modalRoot();
                    if (!root) {
                        return;
                    }
                    if (this.step === 1) {
                        const d = root.querySelector('[name="scheduled_date"]');
                        const t = root.querySelector('[name="scheduled_time"]');
                        const dv = d && String(d.value || '').trim();
                        const tv = t && String(t.value || '').trim();
                        if (!dv || !tv) {
                            alert('Pick appointment date and time.');
                            return;
                        }
                    }
                    if (this.step === 2) {
                        const seat = root.querySelector('[name="table_number"]');
                        if (!seat || !String(seat.value || '').trim()) {
                            alert('Select a seat.');
                            return;
                        }
                        if (empty) {
                            alert('Add bookable services under a Service category in the catalog first, then refresh this page.');
                            return;
                        }
                    }
                    if (this.step < this.maxStep) {
                        this.step++;
                    }
                },
                prev() {
                    if (this.step > 1) {
                        this.step--;
                    }
                },
            };
        }

        let pollingInterval = null;

        function openPaymentModal(orderId, amount) {
            document.getElementById('modalOrderId').value = orderId;
            document.getElementById('modalAmount').innerText = 'Tsh ' + new Intl.NumberFormat().format(amount);
            document.getElementById('paymentModal').classList.remove('hidden');
            document.getElementById('paymentModal').classList.add('flex');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            document.getElementById('paymentModal').classList.remove('flex');
            if (pollingInterval) clearInterval(pollingInterval);
            document.getElementById('selcomPayForm').classList.remove('hidden');
            document.getElementById('pollingStatus').classList.add('hidden');
        }

        function openCreateOrderModal() {
            document.getElementById('createOrderModal').classList.remove('hidden');
            document.getElementById('createOrderModal').classList.add('flex');
            const shell = document.querySelector('#createOrderModal [x-data]');
            if (shell && window.Alpine && typeof Alpine.$data === 'function') {
                try { Alpine.$data(shell).step = 1; } catch (e) {}
            }
        }

        function closeCreateOrderModal() {
            document.getElementById('createOrderModal').classList.add('hidden');
            document.getElementById('createOrderModal').classList.remove('flex');
        }

        document.querySelector('#createOrderModal form')?.addEventListener('submit', function (e) {
            const shell = document.querySelector('#createOrderModal [x-data]');
            let step = 3;
            if (shell && window.Alpine && typeof Alpine.$data === 'function') {
                try {
                    step = Alpine.$data(shell).step;
                } catch (err) {}
            }
            if (step !== 3) {
                e.preventDefault();
                alert('Continue to the last step before creating the booking.');
                return;
            }
            const checked = this.querySelectorAll('input[type="checkbox"]:checked');
            if (checked.length < 1) {
                e.preventDefault();
                alert('Select at least one service.');
            }
        });

        function openEditOrderModal(orderId, tableNumber, phone, name, waiterId, scheduledDate, scheduledTime) {
            const form = document.getElementById('editOrderForm');
            form.action = `/manager/orders/${orderId}`;
            document.getElementById('edit_table_number').value = tableNumber;
            document.getElementById('edit_customer_phone').value = phone ?? '';
            document.getElementById('edit_customer_name').value = name ?? '';
            const waiterSelect = document.getElementById('edit_waiter_id');
            if (waiterSelect) {
                waiterSelect.value = waiterId != null && waiterId !== '' ? String(waiterId) : '';
            }
            const sd = document.getElementById('edit_scheduled_date');
            const st = document.getElementById('edit_scheduled_time');
            if (sd) sd.value = scheduledDate && scheduledDate !== '' ? String(scheduledDate) : '';
            if (st) st.value = scheduledTime && scheduledTime !== '' ? String(scheduledTime) : '';
            document.getElementById('editOrderModal').classList.remove('hidden');
            document.getElementById('editOrderModal').classList.add('flex');
        }

        function closeEditOrderModal() {
            document.getElementById('editOrderModal').classList.add('hidden');
            document.getElementById('editOrderModal').classList.remove('flex');
        }

        function toggleQuantity(index) {
            const checkbox = document.querySelector(`input[name="items[${index}][id]"]`);
            const container = document.getElementById(`qty_container_${index}`);
            const qty = document.getElementById(`qty_${index}`);
            const minus = document.getElementById(`qty_minus_${index}`);
            const plus = document.getElementById(`qty_plus_${index}`);
            if (!checkbox || !qty) return;

            if (checkbox.checked) {
                qty.disabled = false;
                minus.disabled = false;
                plus.disabled = false;
                container.classList.remove('opacity-50');
            } else {
                qty.value = 1;
                qty.disabled = true;
                minus.disabled = true;
                plus.disabled = true;
                container.classList.add('opacity-50');
            }
        }

        function adjustQty(index, change) {
            const input = document.getElementById(`qty_${index}`);
            if (!input || input.disabled) return;
            let val = parseInt(input.value, 10) + change;
            if (val < 1) val = 1;
            input.value = val;
        }

        document.getElementById('selcomPayForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payButton = document.getElementById('payButton');
            const orderId = document.getElementById('modalOrderId').value;
            const phone = document.getElementById('customerPhone').value;
            const name = document.getElementById('customerName').value;

            payButton.disabled = true;
            payButton.innerHTML = '<svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';

            try {
                const response = await fetch('{{ route("manager.payments.selcom.initiate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        phone: phone,
                        name: name,
                        email: 'customer@tiptap.com'
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    document.getElementById('selcomPayForm').classList.add('hidden');
                    document.getElementById('pollingStatus').classList.remove('hidden');
                    startPolling(orderId);
                } else {
                    alert('Error: ' + (result.message || 'Failed to initiate payment'));
                    payButton.disabled = false;
                    payButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg> Send USSD Push';
                }
            } catch (error) {
                alert('Connection error. Please try again.');
                payButton.disabled = false;
                payButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg> Send USSD Push';
            }
        });

        function startPolling(orderId) {
            pollingInterval = setInterval(async () => {
                try {
                    const response = await fetch(`/manager/payments/selcom/status/${orderId}`);
                    const result = await response.json();

                    if (result.status === 'paid') {
                        clearInterval(pollingInterval);
                        alert('Payment Successful!');
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }, 5000);
        }
    </script>

    @php
        $bookingCreateErrors = $errors->hasAny([
            'items', 'scheduled_date', 'scheduled_time', 'table_number', 'waiter_id', 'customer_phone', 'customer_name',
        ]);
        $createWizardStep = $errors->has('items')
            ? 3
            : ($errors->hasAny(['table_number', 'waiter_id', 'customer_phone', 'customer_name']) ? 2 : 1);
    @endphp
    @if($bookingCreateErrors)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                openCreateOrderModal();
                setTimeout(function () {
                    const shell = document.querySelector('#createOrderModal [x-data]');
                    if (!shell || !window.Alpine || typeof Alpine.$data !== 'function') {
                        return;
                    }
                    try {
                        Alpine.$data(shell).step = {{ (int) $createWizardStep }};
                    } catch (e) {}
                }, 80);
            });
        </script>
    @endif
</x-manager-layout>
