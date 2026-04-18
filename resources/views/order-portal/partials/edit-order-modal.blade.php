<div id="editOrderModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-[100] hidden flex items-end sm:items-center justify-center p-4 sm:p-6">
    <div class="bg-[#0f0a1e] w-full max-w-md rounded-2xl shadow-2xl overflow-hidden border border-white/10 max-h-[90vh] flex flex-col">
        <div class="p-4 sm:p-6 border-b border-white/10 flex justify-between items-center shrink-0">
            <h3 class="text-xl font-bold text-white tracking-tight">Edit booking (details)</h3>
            <button type="button" onclick="closeEditOrderModal()" class="p-2 hover:bg-white/10 rounded-xl text-white/40 hover:text-white">✕</button>
        </div>
        <form id="editOrderForm" method="POST" class="p-4 sm:p-6 space-y-4 overflow-y-auto min-h-0">
            @csrf
            @method('PUT')
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.seat') }}</label>
                <select name="table_number" id="edit_table_number" required class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white [&>option]:text-black">
                    @foreach($tables as $table)
                        <option value="{{ $table->name }}">{{ $table->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} name</label>
                <input type="text" name="customer_name" id="edit_customer_name" placeholder="{{ config('salon.customer') }} name" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} phone</label>
                <input type="text" name="customer_phone" id="edit_customer_phone" placeholder="07XXXXXXXX" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500">
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full bg-violet-600 text-white py-3.5 rounded-xl font-bold hover:bg-violet-700 transition-all">Update Details</button>
            </div>
        </form>
    </div>
</div>
<script>
function openEditOrderModal(orderId, tableNumber, phone, name) {
    const form = document.getElementById('editOrderForm');
    form.action = '{{ route("order-portal.orders.update", ["order" => 999]) }}'.replace('999', orderId);
    document.getElementById('edit_table_number').value = tableNumber || '';
    document.getElementById('edit_customer_phone').value = phone || '';
    document.getElementById('edit_customer_name').value = name || '';
    document.getElementById('editOrderModal').classList.remove('hidden');
    document.getElementById('editOrderModal').classList.add('flex');
}
function closeEditOrderModal() {
    document.getElementById('editOrderModal').classList.add('hidden');
    document.getElementById('editOrderModal').classList.remove('flex');
}
</script>
