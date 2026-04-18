<x-manager-layout>
    <x-slot name="header">
        {{ config('salon.live_bookings') }}
    </x-slot>

    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">{{ config('salon.live_bookings') }}</h2>
            <p class="text-sm font-medium text-white/40 uppercase tracking-wider">Real-time service &amp; retail bookings</p>
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
                    <div class="glass p-4 rounded-xl card-hover group">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex flex-col gap-1">
                                <span class="bg-rose-500/20 text-rose-400 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-rose-500/20">{{ config('salon.seat') }} #{{ $order->table_number }}</span>
                                @if($order->waiter)
                                    <span class="text-[10px] font-medium text-cyan-400">{{ $order->waiter->name }}</span>
                                @else
                                    <span class="text-[10px] font-medium text-white/30">Unassigned</span>
                                @endif
                            </div>
                            <span class="text-[10px] font-medium text-white/40">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="space-y-1.5 mb-4">
                            @foreach($order->items as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="font-semibold text-white">{{ $item->quantity }}x {{ $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Custom item') }}</span>
                                    <span class="text-white/40">Tsh {{ number_format($item->total) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-white/5">
                            <span class="font-bold text-white">Tsh {{ number_format($order->total_amount) }}</span>
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
                                <button onclick="openEditOrderModal({{ $order->id }}, '{{ $order->table_number }}', '{{ $order->customer_phone }}', '{{ $order->customer_name }}')" class="p-2 rounded-lg hover:bg-white/10 text-white/40 hover:text-violet-400 transition-all" title="Edit booking">
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
                    <div class="glass p-4 rounded-xl card-hover">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex flex-col gap-1">
                                <span class="bg-amber-500/20 text-amber-400 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-amber-500/20">{{ config('salon.seat') }} #{{ $order->table_number }}</span>
                                @if($order->waiter)
                                    <span class="text-[10px] font-medium text-cyan-400">{{ $order->waiter->name }}</span>
                                @else
                                    <span class="text-[10px] font-medium text-white/30">Unassigned</span>
                                @endif
                            </div>
                            <span class="text-[10px] font-medium text-white/40">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="space-y-1.5 mb-4">
                            @foreach($order->items as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="font-semibold text-white">{{ $item->quantity }}x {{ $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Custom item') }}</span>
                                    <span class="text-white/40">Tsh {{ number_format($item->total) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-white/5">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 {{ $order->status === 'ready' ? 'bg-emerald-400' : 'bg-amber-400' }} rounded-full {{ $order->status === 'ready' ? '' : 'animate-ping' }}"></span>
                                <span class="text-[10px] font-bold {{ $order->status === 'ready' ? 'text-emerald-400' : 'text-amber-400' }} uppercase tracking-wider">{{ $order->status === 'ready' ? 'Ready (floor)' : 'In progress' }}</span>
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
                                <button onclick="openEditOrderModal({{ $order->id }}, '{{ $order->table_number }}', '{{ $order->customer_phone }}', '{{ $order->customer_name }}')" class="p-2 rounded-lg hover:bg-white/10 text-white/40 hover:text-violet-400 transition-all" title="Edit booking">
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
                    <div class="glass p-4 rounded-xl card-hover">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex flex-col gap-1">
                                <span class="bg-emerald-500/20 text-emerald-400 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-emerald-500/20">{{ config('salon.seat') }} #{{ $order->table_number }}</span>
                                @if($order->waiter)
                                    <span class="text-[10px] font-medium text-cyan-400">{{ $order->waiter->name }}</span>
                                @else
                                    <span class="text-[10px] font-medium text-white/30">Unassigned</span>
                                @endif
                            </div>
                            <span class="text-[10px] font-medium text-white/40">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="space-y-1.5 mb-4">
                            @foreach($order->items as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="font-semibold text-white">{{ $item->quantity }}x {{ $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Custom item') }}</span>
                                </div>
                            @endforeach
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
                                <button onclick="openEditOrderModal({{ $order->id }}, '{{ $order->table_number }}', '{{ $order->customer_phone }}', '{{ $order->customer_name }}')" class="h-full px-3 rounded-xl hover:bg-white/10 text-white/40 hover:text-violet-400 transition-all" title="Edit booking">
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
            <div class="space-y-3 opacity-60">
                @forelse($paidOrders as $order)
                    <div class="glass p-4 rounded-xl">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-bold text-white">{{ config('salon.seat') }} #{{ $order->table_number }}</span>
                                @if($order->waiter)
                                    <span class="text-[10px] font-medium text-cyan-400">{{ $order->waiter->name }}</span>
                                @else
                                    <span class="text-[10px] font-medium text-white/30">Unassigned</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
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
                        <p class="text-[11px] font-medium text-white/40">Tsh {{ number_format($order->total_amount) }} • Paid</p>
                    </div>
                @empty
                    <p class="text-sm text-white/30 text-center py-8">No completed bookings today</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Create booking modal -->
    <div id="createOrderModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
        <div class="bg-surface-900 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden border border-white/10 max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white tracking-tight">Create new booking</h3>
                <button onclick="closeCreateOrderModal()" class="p-2 hover:bg-white/10 rounded-xl transition-all text-white/40 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('manager.orders.store') }}" method="POST" class="flex-1 overflow-y-auto p-6 space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.seat') }}</label>
                        <select name="table_number" required class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all [&>option]:text-black">
                            <option value="">Select {{ config('salon.seat') }}</option>
                            @foreach($tables as $table)
                                <option value="{{ $table->name }}">{{ $table->name }} ({{ $table->capacity }} pax)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} name (optional)</label>
                        <input type="text" name="customer_name" placeholder="{{ config('salon.customer') }} name" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} phone (optional)</label>
                    <input type="text" name="customer_phone" placeholder="07XXXXXXXX" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.services') }} &amp; products</label>
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($menuItems as $item)
                            <div class="flex items-center justify-between glass p-3 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" id="item_{{ $item->id }}" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}" class="w-5 h-5 rounded border-white/20 bg-white/5 text-violet-600 focus:ring-violet-500 focus:ring-offset-0" onchange="toggleQuantity({{ $loop->index }})">
                                    <label for="item_{{ $item->id }}" class="text-sm font-medium text-white cursor-pointer select-none">
                                        {{ $item->name }}
                                        <span class="block text-[10px] text-white/40">Tsh {{ number_format($item->price) }}</span>
                                    </label>
                                </div>
                                <div class="flex items-center gap-2 opacity-50 pointer-events-none transition-all" id="qty_container_{{ $loop->index }}">
                                    <button type="button" onclick="adjustQty({{ $loop->index }}, -1)" class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white hover:bg-white/20">-</button>
                                    <input type="number" name="items[{{ $loop->index }}][quantity]" id="qty_{{ $loop->index }}" value="1" min="1" class="w-12 text-center bg-transparent border-none text-white font-bold focus:ring-0 p-0" readonly>
                                    <button type="button" onclick="adjustQty({{ $loop->index }}, 1)" class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white hover:bg-white/20">+</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-white/10">
                    <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-cyan-600 text-white py-3.5 rounded-xl font-bold hover:shadow-lg hover:shadow-violet-500/25 transition-all">
                        Create booking
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div id="editOrderModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
        <div class="bg-surface-900 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden border border-white/10">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white tracking-tight">Edit booking</h3>
                <button onclick="closeEditOrderModal()" class="p-2 hover:bg-white/10 rounded-xl transition-all text-white/40 hover:text-white">
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
                <div class="pt-4">
                    <button type="submit" class="w-full bg-violet-600 text-white py-3.5 rounded-xl font-bold hover:bg-violet-700 transition-all">
                        Update Details
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
        }

        function closeCreateOrderModal() {
            document.getElementById('createOrderModal').classList.add('hidden');
            document.getElementById('createOrderModal').classList.remove('flex');
        }

        function openEditOrderModal(orderId, tableNumber, phone, name) {
            const form = document.getElementById('editOrderForm');
            form.action = `/manager/orders/${orderId}`;
            document.getElementById('edit_table_number').value = tableNumber;
            document.getElementById('edit_customer_phone').value = phone;
            document.getElementById('edit_customer_name').value = name;
            
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
            
            if (checkbox.checked) {
                container.classList.remove('opacity-50', 'pointer-events-none');
            } else {
                container.classList.add('opacity-50', 'pointer-events-none');
            }
        }

        function adjustQty(index, change) {
            const input = document.getElementById(`qty_${index}`);
            let val = parseInt(input.value) + change;
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
</x-manager-layout>
