<x-admin-layout>
    <x-slot name="header">{{ config('salon.staff_plural') }} &amp; unique codes</x-slot>

    <p class="text-white/50 text-sm mb-6 max-w-2xl">Ona {{ strtolower(config('salon.staff_plural')) }} wote kwenye mfumo, nambari zao za pekee (msimbo wa usajili), na {{ strtolower(config('salon.entity_plural')) }} walizounganishwa. Tafuta kwa jina, barua pepe, au nambari ya pekee.</p>

    {{-- Search by unique code --}}
    <div class="glass-card rounded-2xl p-6 mb-6 border border-white/10">
        <h3 class="text-lg font-bold text-white mb-1">Tafuta kwa nambari ya pekee</h3>
        <p class="text-sm text-white/50 mb-4">Ingiza nambari ya {{ strtolower(config('salon.staff')) }} (k.m. msimbo wa usajili) ili kuona maelezo yake.</p>
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="searchCode" class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Nambari ya pekee</label>
                <input type="text" id="searchCode" placeholder="Mfano: 09E26870"
                       class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-mono text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent">
            </div>
            <button type="button" id="adminSearchBtn" onclick="adminSearchWaiter()" class="px-6 py-3 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="search-icon"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <span class="search-label">Tafuta</span>
            </button>
        </div>
        <div id="searchResult" class="mt-4 hidden"></div>
        <div id="searchError" class="mt-4 hidden p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm"></div>
    </div>

    {{-- List --}}
    <div class="glass-card rounded-2xl overflow-hidden border border-white/10">
        <div class="p-6 border-b border-white/5 flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4">
            <div>
                <h2 class="text-xl font-black text-white tracking-tight">Orodha ya {{ config('salon.staff_plural') }}</h2>
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Jina, barua pepe, unique code, {{ strtolower(config('salon.entity')) }}</p>
            </div>
            <form method="GET" action="{{ route('admin.waiters.index') }}" class="flex gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Jina, email, au nambari (mfano 0042)"
                           class="w-full pl-10 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-sm font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="absolute left-3 top-1/2 -translate-y-1/2 text-white/40">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>
                <button type="submit" class="px-5 py-3 bg-white/10 hover:bg-white/15 text-white rounded-xl font-semibold text-sm border border-white/10 transition-all">Tafuta</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]">
                <thead>
                    <tr class="bg-white/5">
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">{{ config('salon.staff') }}</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Unique Code</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">{{ config('salon.entity') }}</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Code</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Orders</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-white/40 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($waiters as $waiter)
                        <tr class="hover:bg-white/5 transition-all group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500/20 to-cyan-500/20 flex items-center justify-center text-violet-400 font-bold text-sm border border-white/10 shrink-0">
                                        {{ substr($waiter->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $waiter->name }}</p>
                                        <p class="text-[10px] text-white/40">{{ $waiter->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-sm font-mono font-bold text-cyan-400 bg-white/5 px-2 py-1 rounded-lg">{{ $waiter->global_waiter_number ?? '—' }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-white/70">{{ $waiter->restaurant?->name ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-xs font-mono text-white/60">{{ $waiter->waiter_code ?? '—' }}</code>
                            </td>
                            <td class="px-6 py-4">
                                @if($waiter->restaurant_id)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span>
                                        Linked
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-white/10 text-white/50 border border-white/10">Not linked</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-white/70 text-sm">{{ $waiter->orders_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.users.show', $waiter) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-white/70 hover:text-white text-xs font-semibold transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-white/50">
                                @if($search)
                                    Hakuna waiter aliyepatikana kwa "{{ $search }}".
                                @else
                                    Hakuna waiters bado kwenye mfumo.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($waiters->hasPages())
            <div class="p-6 border-t border-white/5">
                {{ $waiters->links() }}
            </div>
        @endif
    </div>

    <script>
        function adminSearchWaiter() {
            const input = document.getElementById('searchCode');
            const q = (input?.value || '').trim();
            const resultEl = document.getElementById('searchResult');
            const errorEl = document.getElementById('searchError');
            const btn = document.getElementById('adminSearchBtn');
            const labelEl = btn ? btn.querySelector('.search-label') : null;

            resultEl.classList.add('hidden');
            resultEl.innerHTML = '';
            errorEl.classList.add('hidden');
            errorEl.textContent = '';

            if (!q) {
                errorEl.textContent = 'Ingiza nambari ya pekee (mfano msimbo wa usajili).';
                errorEl.classList.remove('hidden');
                return;
            }

            if (btn) { btn.disabled = true; if (labelEl) labelEl.textContent = 'Inaendesha...'; }

            fetch('{{ route('admin.waiters.search') }}?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        errorEl.textContent = data.message || '{{ config('salon.staff') }} hajapatikana.';
                        errorEl.classList.remove('hidden');
                        return;
                    }
                    const w = data.data;
                    resultEl.innerHTML = `
                        <div class="p-5 rounded-xl bg-white/5 border border-white/10">
                            <div class="flex flex-wrap items-start gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500/20 to-cyan-500/20 flex items-center justify-center text-violet-400 font-bold border border-white/10">${(w.name || '').charAt(0)}</div>
                                    <div>
                                        <p class="font-bold text-white">${w.name || '—'}</p>
                                        <p class="text-xs text-white/50">${w.email || '—'}</p>
                                        <p class="text-sm font-mono text-cyan-400 mt-1">${w.global_waiter_number || '—'}</p>
                                    </div>
                                </div>
                                <div class="text-sm text-white/70">
                                    <p><strong class="text-white/80">{{ config('salon.entity') }}:</strong> ${w.current_restaurant || '—'}</p>
                                    <p><strong class="text-white/80">Code:</strong> ${w.waiter_code || '—'}</p>
                                    <p><strong class="text-white/80">Orders:</strong> ${w.orders_count ?? 0} · <strong class="text-white/80">Feedback:</strong> ${w.feedback_count ?? 0}</p>
                                    <p><strong class="text-white/80">Status:</strong> ${w.is_linked ? 'Linked' : 'Not linked'}</p>
                                </div>
                                <a href="/admin/users/${w.id}" class="ml-auto px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white rounded-lg text-sm font-semibold transition-all">View in User Management</a>
                            </div>
                        </div>`;
                    resultEl.classList.remove('hidden');
                })
                .catch(function() {
                    errorEl.textContent = 'Hitilafu wakati wa kutafuta. Jaribu tena.';
                    errorEl.classList.remove('hidden');
                })
                .finally(function() {
                    if (btn) { btn.disabled = false; if (labelEl) labelEl.textContent = 'Tafuta'; }
                });
        }
    </script>
</x-admin-layout>
