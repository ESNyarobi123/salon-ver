<div id="paymentModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-[100] hidden flex items-end sm:items-center justify-center p-4 sm:p-6">
    <div class="bg-[#0f0a1e] w-full max-w-md rounded-2xl shadow-2xl overflow-hidden border border-white/10 max-h-[90vh] overflow-y-auto">
        <div class="p-4 sm:p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight">Process Payment</h3>
                    <p class="text-sm font-medium text-white/40">Selcom USSD Push</p>
                </div>
                <button type="button" onclick="closePaymentModal()" class="p-2 hover:bg-white/10 rounded-xl text-white/40 hover:text-white">✕</button>
            </div>
            <div class="glass p-5 rounded-xl mb-6 flex justify-between items-center">
                <span class="font-medium text-white/60">Total Amount</span>
                <span id="modalAmount" class="text-2xl font-bold text-white">Tsh 0</span>
            </div>
            <form id="selcomPayForm" class="space-y-4">
                <input type="hidden" id="modalOrderId">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} phone (07XXXXXXXX)</label>
                    <input type="text" id="customerPhone" required placeholder="e.g. 0744963858" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.customer') }} name</label>
                    <input type="text" id="customerName" required placeholder="e.g. John Doe" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500">
                </div>
                <button type="submit" id="payButton" class="w-full bg-gradient-to-r from-violet-600 to-cyan-600 text-white py-3.5 rounded-xl font-semibold hover:shadow-lg transition-all flex items-center justify-center gap-2">Send USSD Push</button>
            </form>
            <div id="pollingStatus" class="hidden mt-6 p-5 bg-cyan-500/10 rounded-xl border border-cyan-500/20 text-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-8 h-8 border-3 border-cyan-400 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm font-semibold text-cyan-400">Waiting for customer to enter PIN...</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var orderPortalPollingInterval = null;
function openPaymentModal(orderId, amount) {
    document.getElementById('modalOrderId').value = orderId;
    document.getElementById('modalAmount').textContent = 'Tsh ' + new Intl.NumberFormat().format(amount);
    document.getElementById('paymentModal').classList.remove('hidden');
    document.getElementById('paymentModal').classList.add('flex');
}
function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentModal').classList.remove('flex');
    if (orderPortalPollingInterval) clearInterval(orderPortalPollingInterval);
    document.getElementById('selcomPayForm').classList.remove('hidden');
    document.getElementById('pollingStatus').classList.add('hidden');
}
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('selcomPayForm');
    if (!form) return;
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        var payButton = document.getElementById('payButton');
        var orderId = document.getElementById('modalOrderId').value;
        var phone = document.getElementById('customerPhone').value;
        var name = document.getElementById('customerName').value;
        payButton.disabled = true;
        payButton.innerHTML = '<span class="animate-spin">⏳</span> Processing...';
        try {
            var r = await fetch('{{ route("order-portal.payments.selcom.initiate") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ order_id: orderId, phone: phone, name: name })
            });
            var result = await r.json();
            if (result.status === 'success') {
                document.getElementById('selcomPayForm').classList.add('hidden');
                document.getElementById('pollingStatus').classList.remove('hidden');
                orderPortalPollingInterval = setInterval(async function() {
                    try {
                        var sr = await fetch('{{ url("order-portal/payments/selcom/status") }}/' + orderId);
                        var s = await sr.json();
                        if (s.status === 'paid') {
                            clearInterval(orderPortalPollingInterval);
                            alert('Payment Successful!');
                            window.location.reload();
                        }
                    } catch (err) { console.error(err); }
                }, 5000);
            } else {
                alert('Error: ' + (result.message || 'Failed to initiate payment'));
                payButton.disabled = false;
                payButton.innerHTML = 'Send USSD Push';
            }
        } catch (err) {
            alert('Connection error. Please try again.');
            payButton.disabled = false;
            payButton.innerHTML = 'Send USSD Push';
        }
    });
});
</script>
