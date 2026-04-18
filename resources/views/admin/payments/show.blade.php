<x-admin-layout>
    <x-slot name="header">
        Transaction Details
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="glass-card rounded-2xl p-8">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Transaction Reference</p>
                    <h2 class="text-2xl font-mono font-black text-white tracking-tight uppercase">{{ $payment->transaction_id ?? 'N/A' }}</h2>
                </div>
                @php
                    $statusColor = match($payment->status) {
                        'completed' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                        'pending' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                        'failed' => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
                        default => 'bg-white/10 text-white/60 border-white/20',
                    };
                @endphp
                <span class="px-6 py-2 rounded-full {{ $statusColor }} text-xs font-black uppercase tracking-widest border">
                    {{ $payment->status }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 py-8 border-t border-white/10">
                <div class="space-y-8">
                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-2">Amount Paid</p>
                        <p class="text-3xl font-black text-white tracking-tight">Tsh {{ number_format($payment->amount, 0) }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-2">Payment Method</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center text-white/60 border border-white/10">
                                <i data-lucide="credit-card" class="w-5 h-5"></i>
                            </div>
                            <p class="text-white font-bold">{{ strtoupper($payment->payment_method) }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-2">Associated Order</p>
                        @if ($payment->order)
                            <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-white font-bold hover:text-violet-400 transition-all flex items-center gap-2">
                                Booking #{{ str_pad($payment->order_id, 6, '0', STR_PAD_LEFT) }}
                                <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                        @else
                            <p class="text-white/60 font-medium">Booking #{{ str_pad($payment->order_id, 6, '0', STR_PAD_LEFT) }} (imefutwa)</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-2">{{ config('salon.entity') }}</p>
                        <p class="text-white font-bold">{{ $payment->order?->restaurant?->name ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-white/10">
                <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-4">Payment Timeline</p>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                        <div class="flex-1 flex justify-between items-center">
                            <p class="text-sm font-bold text-white">Payment Completed</p>
                            <p class="text-xs text-white/40">{{ $payment->updated_at->format('M d, Y • H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-2 h-2 bg-white/20 rounded-full"></div>
                        <div class="flex-1 flex justify-between items-center">
                            <p class="text-sm font-bold text-white/60">Transaction Initiated</p>
                            <p class="text-xs text-white/40">{{ $payment->created_at->format('M d, Y • H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
