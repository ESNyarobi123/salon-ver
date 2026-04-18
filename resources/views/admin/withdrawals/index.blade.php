<x-admin-layout>
    <x-slot name="header">Withdrawal Requests</x-slot>

    <div class="glass-card rounded-2xl overflow-hidden border border-white/10">
        <div class="p-6 border-b border-white/5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight">{{ config('salon.entity') }} withdrawals</h2>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Review and process payout requests</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.withdrawals.index') }}" class="mt-6 flex flex-wrap items-end gap-4">
                <div class="min-w-[160px]">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-1 block">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-sm font-medium text-white focus:ring-2 focus:ring-violet-500 [&>option]:bg-gray-900">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-violet-600 hover:bg-violet-500 text-white rounded-xl font-semibold text-sm transition-all">Filter</button>
                    <a href="{{ route('admin.withdrawals.index') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/15 text-white rounded-xl font-semibold text-sm border border-white/10 transition-all">Clear</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[700px]">
                <thead>
                    <tr class="bg-white/5">
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">{{ config('salon.entity') }}</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Amount</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Method</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-white/40 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($withdrawals as $withdrawal)
                    <tr class="hover:bg-white/5 transition-all">
                        <td class="px-6 py-5">
                            <span class="font-bold text-white">{{ $withdrawal->restaurant?->name ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm text-white font-black">Tsh {{ number_format($withdrawal->amount, 0) }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-white">{{ $withdrawal->payment_method ?? 'N/A' }}</span>
                                <span class="text-[9px] text-white/40 font-medium truncate max-w-[140px]">{{ $withdrawal->payment_details ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $statusColor = match($withdrawal->status) {
                                    'approved' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                    'pending' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                                    'rejected' => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
                                    'paid' => 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30',
                                    default => 'bg-white/10 text-white/60 border-white/20',
                                };
                            @endphp
                            <span class="px-3 py-1.5 rounded-full {{ $statusColor }} text-[10px] font-black uppercase tracking-widest border">{{ $withdrawal->status }}</span>
                        </td>
                        <td class="px-6 py-5 text-sm text-white/60 font-medium">{{ $withdrawal->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-5 text-right">
                            @if($withdrawal->status === 'pending')
                            <div class="flex justify-end gap-2">
                                <button onclick="openModal('approve', {{ $withdrawal->id }})" class="p-2 bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white rounded-xl transition-all" title="Approve"><i data-lucide="check" class="w-4 h-4"></i></button>
                                <button onclick="openModal('reject', {{ $withdrawal->id }})" class="p-2 bg-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white rounded-xl transition-all" title="Reject"><i data-lucide="x" class="w-4 h-4"></i></button>
                            </div>
                            @else
                            <span class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Processed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/30"><i data-lucide="wallet" class="w-8 h-8"></i></div>
                                <p class="text-white font-bold">No withdrawal requests</p>
                                <p class="text-sm text-white/50">Everything is up to date. Try changing the status filter.</p>
                                <a href="{{ route('admin.withdrawals.index') }}" class="text-violet-400 hover:text-violet-300 text-sm font-semibold">Clear filters</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($withdrawals->hasPages())
        <div class="p-6 border-t border-white/5">{{ $withdrawals->links() }}</div>
        @endif
    </div>

    <div id="withdrawalModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden items-center justify-center">
        <div class="glass-card rounded-2xl p-8 w-full max-w-md mx-4 border border-white/10 shadow-2xl">
            <h3 id="modalTitle" class="text-2xl font-black text-white tracking-tight mb-2">Process Withdrawal</h3>
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-6">Add a note for the restaurant manager</p>
            <form id="modalForm" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">Admin Note</label>
                    <textarea name="admin_note" rows="4" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent" placeholder="e.g. Transaction completed via Bank Transfer..."></textarea>
                </div>
                <div class="flex gap-4">
                    <button type="button" onclick="closeModal()" class="flex-1 py-4 glass text-white/60 rounded-xl font-bold text-sm hover:bg-white/10 transition-all">Cancel</button>
                    <button type="submit" id="modalSubmit" class="flex-1 py-4 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl font-bold text-sm hover:shadow-lg transition-all">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(action, id) {
            const modal = document.getElementById('withdrawalModal');
            const form = document.getElementById('modalForm');
            const title = document.getElementById('modalTitle');
            const submit = document.getElementById('modalSubmit');
            title.innerText = action === 'approve' ? 'Approve Withdrawal' : 'Reject Withdrawal';
            submit.innerText = action === 'approve' ? 'Approve' : 'Reject';
            submit.className = action === 'approve'
                ? 'flex-1 py-4 bg-gradient-to-r from-emerald-500 to-cyan-500 text-white rounded-xl font-bold text-sm hover:shadow-lg transition-all'
                : 'flex-1 py-4 bg-gradient-to-r from-rose-500 to-orange-500 text-white rounded-xl font-bold text-sm hover:shadow-lg transition-all';
            form.action = '{{ url('admin/withdrawals') }}/' + id + '/' + action;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal() {
            const modal = document.getElementById('withdrawalModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</x-admin-layout>
