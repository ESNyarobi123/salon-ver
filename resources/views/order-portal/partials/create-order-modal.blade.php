<div id="createOrderModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-[100] hidden flex items-end sm:items-center justify-center p-0 sm:p-6">
    <div class="bg-[#0f0a1e] w-full max-w-2xl rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden border border-white/10 max-h-[92vh] sm:max-h-[90vh] flex flex-col pb-[env(safe-area-inset-bottom)]">
        <div class="p-4 sm:p-6 border-b border-white/10 flex justify-between items-center shrink-0">
            <h3 class="text-xl font-bold text-white tracking-tight">New booking</h3>
            <button type="button" onclick="closeCreateOrderModal()" class="p-2 hover:bg-white/10 rounded-xl transition-all text-white/40 hover:text-white">✕</button>
        </div>
        <form id="orderPortalCreateForm" action="{{ route('order-portal.orders.store') }}" method="POST" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-5 sm:space-y-6 min-h-0">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.seat') }}</label>
                    <select name="table_number" required class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white focus:ring-2 focus:ring-violet-500 [&>option]:text-black">
                        <option value="">Select {{ config('salon.seat') }}</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->name }}">{{ $table->name }} ({{ $table->capacity }} pax)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} name (optional)</label>
                    <input type="text" name="customer_name" placeholder="{{ config('salon.customer') }} name" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500">
                </div>
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} phone (optional)</label>
                <input type="text" name="customer_phone" placeholder="07XXXXXXXX" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.services') }} &amp; products</label>
                <p class="text-[11px] text-white/45 mb-2">Tick each line you want; you can add several services/products in one booking.</p>
                <div class="space-y-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($menuItems as $item)
                        <div class="flex items-center justify-between glass p-3 rounded-xl">
                            <div class="flex items-center gap-3 min-w-0">
                                @if($item->image)
                                    <img src="{{ $item->imageUrl() }}" alt="" class="w-12 h-12 rounded-lg object-cover shrink-0 border border-white/10">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-white/5 shrink-0 flex items-center justify-center text-white/30">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <input type="checkbox" id="create_item_{{ $item->id }}" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}" class="w-5 h-5 rounded border-white/20 bg-white/5 text-violet-600 focus:ring-violet-500" onchange="toggleCreateQty({{ $loop->index }})">
                                    <label for="create_item_{{ $item->id }}" class="text-sm font-medium text-white cursor-pointer ml-2">{{ $item->name }} <span class="block text-[10px] text-white/40">Tsh {{ number_format($item->price) }}</span></label>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 opacity-50 transition-all shrink-0" id="create_qty_container_{{ $loop->index }}">
                                <button type="button" id="create_qty_minus_{{ $loop->index }}" onclick="adjustCreateQty({{ $loop->index }}, -1)" disabled class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white hover:bg-white/20 disabled:opacity-30 disabled:pointer-events-none">−</button>
                                <input type="number" name="items[{{ $loop->index }}][quantity]" id="create_qty_{{ $loop->index }}" value="1" min="1" disabled class="w-12 text-center bg-transparent border-none text-white font-bold p-0 disabled:opacity-50" readonly>
                                <button type="button" id="create_qty_plus_{{ $loop->index }}" onclick="adjustCreateQty({{ $loop->index }}, 1)" disabled class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white hover:bg-white/20 disabled:opacity-30 disabled:pointer-events-none">+</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="pt-4 border-t border-white/10">
                <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-cyan-600 text-white py-3.5 rounded-xl font-bold hover:shadow-lg transition-all">Create booking</button>
            </div>
        </form>
    </div>
</div>
<script>
function openCreateOrderModal() {
    document.getElementById('createOrderModal').classList.remove('hidden');
    document.getElementById('createOrderModal').classList.add('flex');
}
function closeCreateOrderModal() {
    document.getElementById('createOrderModal').classList.add('hidden');
    document.getElementById('createOrderModal').classList.remove('flex');
}
function toggleCreateQty(i) {
    const cb = document.querySelector('input[name="items['+i+'][id]"]');
    const container = document.getElementById('create_qty_container_'+i);
    const qty = document.getElementById('create_qty_'+i);
    const minus = document.getElementById('create_qty_minus_'+i);
    const plus = document.getElementById('create_qty_plus_'+i);
    if (!cb || !qty) return;
    if (cb.checked) {
        qty.disabled = false;
        if (minus) minus.disabled = false;
        if (plus) plus.disabled = false;
        container.classList.remove('opacity-50');
    } else {
        qty.value = 1;
        qty.disabled = true;
        if (minus) minus.disabled = true;
        if (plus) plus.disabled = true;
        container.classList.add('opacity-50');
    }
}
function adjustCreateQty(i, d) {
    const el = document.getElementById('create_qty_'+i);
    if (!el || el.disabled) return;
    let v = parseInt(el.value || 0, 10) + d;
    if (v < 1) v = 1;
    el.value = v;
}
document.getElementById('orderPortalCreateForm')?.addEventListener('submit', function (e) {
    const checked = this.querySelectorAll('input[type="checkbox"]:checked');
    if (checked.length < 1) {
        e.preventDefault();
        alert('Select at least one service or product.');
    }
});
</script>
