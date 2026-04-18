@php
    $badgeClass = match($status) {
        'pending' => 'bg-rose-500/20 text-rose-400 border-rose-500/20',
        'preparing' => 'bg-amber-500/20 text-amber-400 border-amber-500/20',
        'served' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/20',
        default => 'bg-rose-500/20 text-rose-400 border-rose-500/20',
    };
@endphp
<div class="glass p-4 rounded-xl card-hover border border-white/5 min-w-0 overflow-hidden">
    <div class="flex justify-between items-start gap-2 mb-3">
        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border {{ $badgeClass }} shrink-0" title="{{ config('salon.seat') }}">S#{{ $order->table_number }}</span>
        <span class="text-[10px] font-medium text-white/40 shrink-0 whitespace-nowrap">{{ $order->created_at->diffForHumans() }}</span>
    </div>
    <div class="space-y-2 mb-4 max-h-28 sm:max-h-32 overflow-y-auto custom-scrollbar">
        @foreach($order->items as $item)
            <div class="flex justify-between items-center gap-2 text-xs sm:text-sm min-w-0">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    @if($item->menuItem && $item->menuItem->image)
                        <img src="{{ $item->menuItem->imageUrl() }}" alt="" class="w-6 h-6 sm:w-8 sm:h-8 rounded object-cover shrink-0 border border-white/10">
                    @endif
                    <span class="font-medium text-white truncate">{{ $item->quantity }}× {{ $item->name ?? ($item->menuItem?->name ?? 'Item') }}</span>
                </div>
                <span class="text-white/40 shrink-0 text-xs">Tsh {{ number_format($item->total) }}</span>
            </div>
        @endforeach
    </div>
    <div class="pt-3 border-t border-white/5 space-y-3">
        <div class="flex items-center justify-between gap-2 min-w-0">
            <span class="font-bold text-white text-sm sm:text-base truncate">Tsh {{ number_format($order->total_amount) }}</span>
        </div>
        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
            <form action="{{ route('order-portal.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Delete this order?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="min-w-[40px] min-h-[40px] sm:min-w-[36px] sm:min-h-[36px] inline-flex items-center justify-center rounded-lg hover:bg-white/10 text-white/40 hover:text-rose-400 transition-colors touch-action-manipulation" title="Delete" aria-label="Delete order">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
            <button type="button" onclick="openEditOrderModal({{ $order->id }}, {{ json_encode($order->table_number) }}, {{ json_encode($order->customer_phone ?? '') }}, {{ json_encode($order->customer_name ?? '') }})" class="min-w-[40px] min-h-[40px] sm:min-w-[36px] sm:min-h-[36px] inline-flex items-center justify-center rounded-lg hover:bg-white/10 text-white/40 hover:text-violet-400 transition-colors touch-action-manipulation" title="Edit details" aria-label="Edit details">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>
            <button type="button" onclick="openEditItemsModal({{ $order->id }}, {{ json_encode($order->items->map(fn($i) => ['id' => $i->menu_item_id, 'quantity' => $i->quantity])->values()->all()) }})" class="min-w-[40px] min-h-[40px] sm:min-w-[36px] sm:min-h-[36px] inline-flex items-center justify-center rounded-lg hover:bg-white/10 text-white/40 hover:text-cyan-400 transition-colors touch-action-manipulation" title="Edit items" aria-label="Edit items">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </button>
            @if($status === 'pending')
                <form action="{{ route('order-portal.orders.update', $order) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="preparing">
                    <button type="submit" class="min-w-[40px] min-h-[40px] sm:min-w-[36px] sm:min-h-[36px] inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-violet-600 to-cyan-600 text-white hover:opacity-90 transition-opacity touch-action-manipulation" title="Start Preparing" aria-label="Move to Preparing">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </button>
                </form>
            @endif
            @if($status === 'preparing')
                <form action="{{ route('order-portal.orders.update', $order) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="served">
                    <button type="submit" class="min-w-[40px] min-h-[40px] sm:min-w-[36px] sm:min-h-[36px] inline-flex items-center justify-center rounded-lg bg-emerald-500 text-white hover:bg-emerald-600 transition-colors touch-action-manipulation" title="Mark Served" aria-label="Mark Served">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
                    </button>
                </form>
            @endif
            @if($status === 'served')
                <button type="button" onclick="openPaymentModal({{ $order->id }}, {{ $order->total_amount }})" class="min-h-[40px] sm:min-h-[36px] px-3 py-2 rounded-lg bg-gradient-to-r from-violet-600 to-cyan-600 text-white text-xs font-semibold hover:opacity-90 transition-opacity touch-action-manipulation shadow-lg shadow-violet-500/20">Pay</button>
                <form action="{{ route('order-portal.orders.update', $order) }}" method="POST" class="inline" onsubmit="return confirm('Thibitisha mteja kashalipia (mfano kwa WhatsApp/cash)?');">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="paid">
                    <button type="submit" class="min-h-[40px] sm:min-h-[36px] px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition-colors touch-action-manipulation border border-emerald-500/30" title="Mteja kashalipia nje (WhatsApp/cash)">Confirm paid</button>
                </form>
            @endif
        </div>
    </div>
</div>
