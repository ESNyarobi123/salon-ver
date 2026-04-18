<x-admin-layout>
    <x-slot name="header">
        Booking details
    </x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Booking info -->
            <div class="lg:col-span-2 space-y-8">
                <div class="glass-card rounded-2xl p-8">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h2 class="text-3xl font-black text-white tracking-tight">Booking #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h2>
                            <p class="text-white/40 font-bold uppercase tracking-widest text-[10px] mt-1">Placed on {{ $order->created_at->format('M d, Y • H:i') }}</p>
                        </div>
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
                        <span class="px-6 py-2 rounded-full {{ $statusColor }} text-xs font-black uppercase tracking-widest border">
                            {{ $order->status }}
                        </span>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-sm font-black text-white uppercase tracking-widest border-b border-white/10 pb-4">Services &amp; products</h3>
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                            <div class="flex justify-between items-center p-4 bg-white/5 rounded-xl border border-white/10">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-xl flex items-center justify-center font-black text-violet-400 border border-violet-500/20">
                                        {{ $item->quantity }}x
                                    </div>
                                    <div>
                                        <p class="font-bold text-white">{{ $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Custom item') }}</p>
                                        <p class="text-[10px] text-white/40 font-bold uppercase tracking-widest">Tsh {{ number_format($item->price, 0) }} per unit</p>
                                    </div>
                                </div>
                                <p class="font-black text-white">Tsh {{ number_format($item->price * $item->quantity, 0) }}</p>
                            </div>
                            @endforeach
                        </div>

                        <div class="pt-6 border-t border-white/10 space-y-2">
                            <div class="flex justify-between items-center text-white/40 font-bold uppercase tracking-widest text-[10px]">
                                <span>Subtotal</span>
                                <span>Tsh {{ number_format($order->total_amount, 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-white font-black text-xl tracking-tight pt-2">
                                <span>Total Amount</span>
                                <span>Tsh {{ number_format($order->total_amount, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saloon & stylist -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="glass-card rounded-2xl p-6">
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-4">{{ config('salon.entity') }}</p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-violet-600 to-cyan-500 rounded-2xl flex items-center justify-center text-white font-black">
                                {{ substr($order->restaurant->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-white">{{ $order->restaurant->name }}</p>
                                <p class="text-[10px] text-white/40 font-medium">{{ $order->restaurant->location }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6">
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-4">Assigned {{ config('salon.staff') }}</p>
                        @if($order->waiter)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-orange-500/20 text-orange-400 rounded-2xl flex items-center justify-center font-black border border-orange-500/20">
                                {{ substr($order->waiter->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-white">{{ $order->waiter->name }}</p>
                                <p class="text-[10px] text-white/40 font-medium">{{ config('salon.admin_order_stylist_internal_id') }}{{ $order->waiter->id }}</p>
                            </div>
                        </div>
                        @else
                        <p class="text-sm font-bold text-white/40 italic">No {{ strtolower(config('salon.staff')) }} assigned</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="space-y-8">
                <div class="glass-card rounded-2xl p-6 border border-violet-500/20">
                    <h3 class="text-xl font-black text-white tracking-tight mb-6">Payment Status</h3>
                    @if($order->payment)
                    <div class="space-y-6">
                        <div class="p-5 bg-white/5 rounded-xl border border-white/10">
                            <p class="text-[10px] font-black text-white/50 uppercase tracking-widest mb-1">Transaction ID</p>
                            <p class="font-mono text-xs text-white truncate">{{ $order->payment->transaction_id }}</p>
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-[10px] font-black text-white/50 uppercase tracking-widest">Method</p>
                                <p class="font-bold text-white">{{ strtoupper($order->payment->payment_method) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-white/50 uppercase tracking-widest">Status</p>
                                <span class="px-3 py-1 bg-emerald-500 text-white text-[9px] font-black rounded-full uppercase tracking-widest">{{ $order->payment->status }}</span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-8 text-center bg-white/5 rounded-xl border border-white/10">
                        <i data-lucide="credit-card" class="w-10 h-10 text-white/20 mb-4"></i>
                        <p class="text-sm font-bold text-white">No Payment Found</p>
                        <p class="text-[10px] text-white/40 font-medium uppercase tracking-widest mt-1">Awaiting customer action</p>
                    </div>
                    @endif
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <h3 class="text-xl font-black text-white tracking-tight mb-6">Actions</h3>
                    <div class="space-y-3">
                        <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status" onchange="this.form.submit()" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all mb-4 [&>option]:text-black">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Mark as Pending</option>
                                <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Mark as Preparing</option>
                                <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Mark as Ready</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Mark as Completed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Mark as Cancelled</option>
                            </select>
                        </form>
                        <button class="w-full py-4 glass text-white rounded-xl font-bold text-sm hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                            <i data-lucide="printer" class="w-4 h-4"></i> Print Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
