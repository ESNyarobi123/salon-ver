<div id="editItemsModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-[100] hidden flex items-end sm:items-center justify-center p-0 sm:p-6">
    <div class="bg-[#0f0a1e] w-full max-w-2xl rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden border border-white/10 max-h-[92vh] flex flex-col pb-[env(safe-area-inset-bottom)]">
        <div class="p-4 sm:p-6 border-b border-white/10 flex justify-between items-center shrink-0">
            <h3 class="text-xl font-bold text-white tracking-tight">Edit booking ({{ config('salon.services') }})</h3>
            <button type="button" onclick="closeEditItemsModal()" class="p-2 hover:bg-white/10 rounded-xl text-white/40 hover:text-white">✕</button>
        </div>
        <form id="editItemsForm" method="POST" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 min-h-0">
            @csrf
            @method('PUT')
            <p class="text-sm text-white/50">Chagua bidhaa na idadi. Ondoa bidhaa kwa kuweka 0.</p>
            <div class="space-y-3 max-h-72 overflow-y-auto pr-2 custom-scrollbar" id="editItemsList">
                @foreach($menuItems as $idx => $item)
                    <div class="flex items-center justify-between glass p-3 rounded-xl">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            @if($item->image)
                                <img src="{{ $item->imageUrl() }}" alt="" class="w-10 h-10 rounded-lg object-cover shrink-0 border border-white/10">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-white/5 shrink-0 flex items-center justify-center text-white/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                </div>
                            @endif
                            <label class="text-sm font-medium text-white cursor-pointer">{{ $item->name }} <span class="text-[10px] text-white/40">Tsh {{ number_format($item->price) }}</span></label>
                        </div>
                        <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item->id }}">
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="editItemsQty({{ $idx }}, -1)" class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white hover:bg-white/20">−</button>
                            <input type="number" name="items[{{ $idx }}][quantity]" id="edit_item_qty_{{ $idx }}" value="0" min="0" class="w-14 text-center bg-white/5 border border-white/10 rounded-lg text-white font-bold py-1">
                            <button type="button" onclick="editItemsQty({{ $idx }}, 1)" class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white hover:bg-white/20">+</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="pt-4 border-t border-white/10">
                <button type="submit" class="w-full bg-cyan-600 text-white py-3.5 rounded-xl font-bold hover:bg-cyan-700 transition-all">Update Items</button>
            </div>
        </form>
    </div>
</div>
@php
    $menuIdToIndex = [];
    foreach ($menuItems as $idx => $item) {
        $menuIdToIndex[$item->id] = $idx;
    }
@endphp
<script>
function openEditItemsModal(orderId, currentItems) {
    document.getElementById('editItemsForm').action = '{{ route("order-portal.orders.update", ["order" => 999]) }}'.replace('999', orderId);
    @foreach($menuItems as $idx => $item)
    (function(){
        var el = document.getElementById('edit_item_qty_{{ $idx }}');
        if (el) el.value = 0;
    })();
    @endforeach
    var idToIndex = @json($menuIdToIndex);
    if (currentItems && Array.isArray(currentItems)) {
        currentItems.forEach(function(row) {
            var idx = idToIndex[row.id];
            if (idx !== undefined) {
                var el = document.getElementById('edit_item_qty_' + idx);
                if (el) el.value = row.quantity || 0;
            }
        });
    }
    document.getElementById('editItemsModal').classList.remove('hidden');
    document.getElementById('editItemsModal').classList.add('flex');
}
function closeEditItemsModal() {
    document.getElementById('editItemsModal').classList.add('hidden');
    document.getElementById('editItemsModal').classList.remove('flex');
}
function editItemsQty(idx, delta) {
    var el = document.getElementById('edit_item_qty_' + idx);
    if (!el) return;
    var v = parseInt(el.value || 0, 10) + delta;
    if (v < 0) v = 0;
    el.value = v;
}
</script>
