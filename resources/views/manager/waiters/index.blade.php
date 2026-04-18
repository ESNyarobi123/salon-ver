<x-manager-layout>
    <x-slot name="header">{{ config('salon.staff_plural') }} &amp; team</x-slot>

    @if (session('success') && !session('order_portal_password_generated'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">{{ session('error') }}</div>
    @endif
    @if (session('order_portal_password_generated'))
        <div class="mb-6 p-4 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 text-sm">
            {{ session('success') }}
            <p class="mt-2 font-semibold">Nenosiri la Service desk (onyesha {{ strtolower(config('salon.staff')) }} mara moja):</p>
            <p class="mt-1 font-mono text-lg tracking-wider bg-black/20 px-3 py-2 rounded-lg inline-block">{{ session('order_portal_password_generated') }}</p>
            <p class="mt-2 text-white/70">{{ config('salon.staff') }}: <strong>{{ session('order_portal_waiter_name') }}</strong> · Nambari: <code>{{ session('order_portal_waiter_number') }}</code></p>
            <p class="mt-1 text-white/50 text-xs">Login: <a href="{{ $orderPortalLoginUrl ?? route('order-portal.login') }}" class="text-cyan-400 underline" target="_blank">{{ $orderPortalLoginUrl ?? url('/order-portal/login') }}</a></p>
        </div>
    @endif

    <!-- Link stylist card -->
    <div class="glass-card rounded-2xl p-6 mb-8 border border-white/10">
        <h3 class="text-lg font-bold text-white mb-1">Unganisha {{ config('salon.staff') }}</h3>
        <p class="text-sm text-white/50 mb-2">{{ config('salon.staff') }} anajisajili kwenye wavuti, kisha anakupa nambari yake ya pekee (TIPTAP-W-xxxxx). Tafuta hapa na uunganishe na {{ strtolower(config('salon.entity')) }} yako.</p>
        <p class="text-xs text-white/40 mb-4">Chagua <strong class="text-white/60">Muda mrefu</strong> (permanent) au <strong class="text-white/60">Show-time</strong> (muda maalum – weka tarehe ya mwisho).</p>
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="searchCode" class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Nambari ya pekee ya {{ strtolower(config('salon.staff')) }}</label>
                <input type="text" id="searchCode" placeholder="TIPTAP-W-00001"
                       class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl font-mono text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent">
            </div>
            <button type="button" onclick="searchWaiter()" class="px-6 py-3 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                Tafuta
            </button>
        </div>
        <div id="searchResult" class="mt-4 hidden"></div>
        <div id="searchError" class="mt-4 hidden p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm"></div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="glass-card rounded-xl p-4 mb-6 border border-white/10" x-data="{ filterOpen: false }">
        <div class="flex flex-wrap gap-3 items-center justify-between">
            <div class="flex-1 min-w-[250px] max-w-md">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="absolute left-3 top-1/2 -translate-y-1/2 text-white/40">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                    <input type="text" id="waiterSearch" placeholder="Search by name or code..." 
                           class="w-full pl-10 pr-4 py-2 bg-white/5 border border-white/10 rounded-lg text-sm text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                           oninput="filterWaiters()">
                </div>
            </div>
            <div class="flex gap-2">
                <button @click="filterOpen = !filterOpen" class="px-4 py-2 glass rounded-lg border border-white/10 text-white/70 hover:text-white hover:bg-white/10 transition-all text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    Filter
                </button>
                <select id="sortBy" onchange="filterWaiters()" class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-sm text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                    <option value="name">Sort: Name</option>
                    <option value="orders">Sort: {{ config('salon.booking_plural') }}</option>
                    <option value="recent">Sort: Recently Added</option>
                </select>
                <a href="{{ route('manager.waiters.history') }}" class="inline-flex items-center gap-2 px-4 py-2 glass rounded-lg border border-white/10 text-white/70 hover:text-white hover:bg-white/10 transition-all text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0 11 18 0z"/></svg>
                    History
                </a>
            </div>
        </div>
        
        <!-- Filter Options -->
        <div x-show="filterOpen" x-transition class="mt-4 pt-4 border-t border-white/10 flex flex-wrap gap-3">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="radio" name="statusFilter" value="all" checked onchange="filterWaiters()" class="text-violet-600 focus:ring-violet-500">
                <span class="text-sm text-white/70">All {{ config('salon.staff_plural') }}</span>
            </label>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="radio" name="statusFilter" value="online" onchange="filterWaiters()" class="text-violet-600 focus:ring-violet-500">
                <span class="text-sm text-white/70">Online Only</span>
            </label>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="radio" name="statusFilter" value="offline" onchange="filterWaiters()" class="text-sm text-violet-600 focus:ring-violet-500">
                <span class="text-sm text-white/70">Offline</span>
            </label>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="radio" name="statusFilter" value="permanent" onchange="filterWaiters()" class="text-violet-600 focus:ring-violet-500">
                <span class="text-sm text-white/70">Permanent</span>
            </label>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="radio" name="statusFilter" value="temporary" onchange="filterWaiters()" class="text-violet-600 focus:ring-violet-500">
                <span class="text-sm text-white/70">Temporary</span>
            </label>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-white">Active Staff <span id="waiterCount" class="text-white/50 font-normal text-base"></span></h2>
    </div>

    <div id="waitersGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($waiters as $waiter)
            <div class="waiter-card glass-card p-4 rounded-xl card-hover group transition-all" 
                 data-name="{{ strtolower($waiter->name) }}" 
                 data-code="{{ strtolower($waiter->global_waiter_number ?? '') }}" 
                 data-status="{{ $waiter->is_online ? 'online' : 'offline' }}" 
                 data-employment="{{ $waiter->employment_type }}" 
                 data-orders="{{ $waiter->orders_count }}">
                <!-- Avatar & Name -->
                <div class="flex items-center gap-3 mb-3">
                    @php 
                        $waiterPhotoUrl = $waiter->profilePhotoUrl(); 
                        $initials = substr($waiter->name, 0, 1);
                        $colorHash = crc32($waiter->global_waiter_number ?? $waiter->name) % 6;
                        $colors = [
                            'from-violet-500/20 to-purple-500/20 text-violet-400',
                            'from-blue-500/20 to-cyan-500/20 text-blue-400',
                            'from-emerald-500/20 to-teal-500/20 text-emerald-400',
                            'from-amber-500/20 to-orange-500/20 text-amber-400',
                            'from-rose-500/20 to-pink-500/20 text-rose-400',
                            'from-indigo-500/20 to-blue-500/20 text-indigo-400',
                        ];
                        $avatarColor = $colors[$colorHash];
                    @endphp
                    @if($waiterPhotoUrl)
                        <img src="{{ $waiterPhotoUrl }}" alt="{{ $waiter->name }}" loading="lazy" class="w-12 h-12 rounded-lg object-cover border border-white/10 group-hover:scale-105 transition-transform shrink-0" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="waiter-fallback-avatar w-12 h-12 bg-gradient-to-br {{ $avatarColor }} rounded-lg flex items-center justify-center font-bold text-lg border border-white/10 shrink-0 hidden">
                            {{ $initials }}
                        </div>
                    @else
                        <div class="w-12 h-12 bg-gradient-to-br {{ $avatarColor }} rounded-lg flex items-center justify-center font-bold text-lg border border-white/10 group-hover:scale-105 transition-transform shrink-0">
                            {{ $initials }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <h4 class="text-sm font-bold text-white truncate">{{ $waiter->name }}</h4>
                        <p class="text-[10px] font-mono text-cyan-400/80">{{ $waiter->global_waiter_number ?? '—' }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            @if($waiter->is_online)
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1 h-1 bg-emerald-400 rounded-full animate-pulse"></span>
                                    Online
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-medium bg-white/5 text-white/40 border border-white/10">
                                    <span class="w-1 h-1 bg-white/40 rounded-full"></span>
                                    Offline
                                </span>
                            @endif
                            @if($waiter->employment_type === 'temporary')
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-semibold bg-amber-500/20 text-amber-400 border border-amber-500/30">Temp</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($waiter->waiter_code)
                <div class="mb-4 bg-white/5 rounded-xl overflow-hidden border border-white/10">
                    <div class="p-3 bg-white/5 border-b border-white/5 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-white/40 uppercase tracking-wider">Service Tag</span>
                        <div class="flex items-center gap-2">
                            <code class="text-sm font-mono font-bold text-cyan-400">{{ $waiter->waiter_code }}</code>
                            <button onclick="copyToClipboard('{{ $waiter->waiter_code }}', this)" class="text-white/40 hover:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 flex items-center gap-4">
                        <div class="bg-white p-2 rounded-lg shrink-0">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($waiter->waiter_qr_url) }}" alt="QR" class="w-16 h-16">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] text-white/40 mb-2 truncate">Scan to book with {{ explode(' ', $waiter->name)[0] }}</p>
                            <div class="flex gap-2">
                                <a href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode($waiter->waiter_qr_url) }}" download="waiter-{{ $waiter->waiter_code }}-qr.png" target="_blank" class="flex-1 px-3 py-2 bg-violet-600 hover:bg-violet-500 text-white text-[10px] font-bold uppercase tracking-wider rounded-lg text-center transition-colors">Download</a>
                                <button onclick="copyToClipboard('{{ $waiter->waiter_qr_url }}', this)" class="px-3 py-2 glass hover:bg-white/10 text-white text-[10px] font-bold uppercase tracking-wider rounded-lg transition-colors">Copy Link</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Mini Stats -->
                <div class="grid grid-cols-3 gap-2 mb-3">
                    <div class="bg-white/5 p-2 rounded-lg border border-white/10">
                        <p class="text-[9px] font-bold text-white/40 uppercase tracking-wider mb-0.5">{{ config('salon.booking_plural') }}</p>
                        <p class="text-lg font-bold text-white">{{ $waiter->orders_count }}</p>
                        <p class="text-[8px] text-white/30">All time</p>
                    </div>
                    <div class="bg-white/5 p-2 rounded-lg border border-white/10">
                        <p class="text-[9px] font-bold text-white/40 uppercase tracking-wider mb-0.5">Tips</p>
                        <p class="text-lg font-bold text-amber-400">{{ number_format($waiter->tips_sum_amount ?? 0) }}</p>
                        <p class="text-[8px] text-white/30">TSh</p>
                    </div>
                    <div class="bg-white/5 p-2 rounded-lg border border-white/10">
                        <p class="text-[9px] font-bold text-white/40 uppercase tracking-wider mb-0.5">Rating</p>
                        <p class="text-lg font-bold text-white flex items-center gap-0.5">
                            {{ number_format($waiter->feedback_avg_rating ?? 0, 1) }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="text-amber-400">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        </p>
                        <p class="text-[8px] text-white/30">{{ $waiter->feedback_count ?? 0 }} reviews</p>
                    </div>
                </div>

                @if(($waiter->feedback_avg_rating ?? 0) >= 4.5 && ($waiter->feedback_count ?? 0) >= 10)
                    <div class="mb-3 px-2 py-1 bg-gradient-to-r from-amber-500/20 to-orange-500/20 border border-amber-500/30 rounded-lg flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-amber-400">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider">Top Performer 🏆</span>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="flex gap-1.5">
                    @php
                        $waiterForModal = $waiter->only(['id','name','email','waiter_code','global_waiter_number','orders_count','created_at','employment_type','linked_until']);
                        $waiterForModal['profile_photo_url'] = $waiter->profilePhotoUrl();
                        $waiterForModal['tips_sum'] = $waiter->tips_sum_amount ?? 0;
                        $waiterForModal['rating'] = $waiter->feedback_avg_rating ?? 0;
                        $waiterForModal['rating_count'] = $waiter->feedback_count ?? 0;
                        $hasOrderPortal = in_array($waiter->id, $waiterIdsWithOrderPortal ?? []);
                    @endphp
                    <button onclick="openViewWaiterModal({{ json_encode($waiterForModal) }})" class="flex-1 px-3 py-2 glass rounded-lg font-medium text-white/70 hover:text-white hover:bg-violet-600 transition-all text-xs" title="View Profile">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                    <form action="{{ route('manager.waiters.generate-order-portal-password', $waiter) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 rounded-lg font-medium text-xs transition-all {{ $hasOrderPortal ? 'glass text-cyan-400 hover:bg-cyan-500/20' : 'bg-cyan-600 hover:bg-cyan-500 text-white' }}" title="{{ $hasOrderPortal ? 'Regenerate' : 'Generate Portal' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </button>
                    </form>
                    <form action="{{ route('manager.waiters.unlink', $waiter) }}" method="POST" onsubmit="return confirm('Unlink {{ strtolower(config('salon.staff')) }} huyu? History itabaki.');" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 glass text-rose-400 rounded-lg hover:bg-rose-500/20 transition-all text-xs" title="Unlink">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full glass-card py-16 text-center rounded-2xl">
                <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white/20">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Hakuna {{ strtolower(config('salon.staff_plural')) }} waliounganishwa</h3>
                <p class="text-white/40">Tumia "Unganisha {{ config('salon.staff') }}" hapo juu kwa nambari ya pekee.</p>
            </div>
        @endforelse
    </div>

    <!-- View Waiter Modal -->
    <div id="viewWaiterModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6">
        <div class="bg-surface-900 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden border border-white/10">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white tracking-tight">{{ config('salon.staff') }} profile</h3>
                        <p class="text-sm font-medium text-white/40">Staff details and performance</p>
                    </div>
                    <button onclick="closeViewWaiterModal()" class="p-2 hover:bg-white/10 rounded-xl transition-all text-white/40 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <div class="flex items-center gap-5 mb-6">
                    <div class="w-20 h-20 rounded-2xl border border-violet-500/20 overflow-hidden shrink-0" id="viewWaiterPhotoWrap">
                        <img id="viewWaiterPhoto" src="" alt="" class="w-full h-full object-cover hidden">
                        <div id="viewWaiterInitial" class="w-full h-full bg-gradient-to-br from-violet-500/20 to-cyan-500/20 flex items-center justify-center font-bold text-3xl text-violet-400">—</div>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-white" id="viewWaiterName">—</h4>
                        <p class="text-sm text-white/40" id="viewWaiterEmail">—</p>
                        <p class="text-xs font-mono text-cyan-400 mt-1" id="viewWaiterGlobalCode">—</p>
                        <div class="mt-2 inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse mr-2"></span>
                            Active Staff
                        </div>
                        <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mt-2 mb-0.5">Aina ya kuunga</p>
                        <p class="text-xs text-white/70" id="viewWaiterEmployment">—</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white/5 p-4 rounded-xl border border-white/5">
                        <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-1">Service Tag</p>
                        <p class="text-lg font-mono font-bold text-cyan-400" id="viewWaiterCode">—</p>
                    </div>
                    <div class="bg-white/5 p-4 rounded-xl border border-white/5">
                        <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-1">Joined Date</p>
                        <p class="text-lg font-bold text-white" id="viewWaiterJoined">—</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="bg-violet-500/10 p-3 rounded-xl border border-violet-500/20">
                        <p class="text-[9px] font-bold text-violet-300 uppercase tracking-wider mb-1">{{ config('salon.booking_plural') }}</p>
                        <p class="text-xl font-bold text-white" id="viewWaiterOrders">0</p>
                    </div>
                    <div class="bg-amber-500/10 p-3 rounded-xl border border-amber-500/20">
                        <p class="text-[9px] font-bold text-amber-300 uppercase tracking-wider mb-1">Tips</p>
                        <p class="text-xl font-bold text-amber-400" id="viewWaiterTips">0</p>
                        <p class="text-[8px] text-white/40">TSh</p>
                    </div>
                    <div class="bg-emerald-500/10 p-3 rounded-xl border border-emerald-500/20">
                        <p class="text-[9px] font-bold text-emerald-300 uppercase tracking-wider mb-1">Rating</p>
                        <p class="text-xl font-bold text-white flex items-center gap-1" id="viewWaiterRating">0.0 ⭐</p>
                        <p class="text-[8px] text-white/40" id="viewWaiterRatingCount">0 reviews</p>
                    </div>
                </div>

                <button onclick="closeViewWaiterModal()" class="w-full bg-white/10 text-white py-3.5 rounded-xl font-semibold hover:bg-white/20 transition-all">Close Profile</button>
            </div>
        </div>
    </div>

    <script>
        function searchWaiter() {
            const q = document.getElementById('searchCode').value.trim();
            const resultEl = document.getElementById('searchResult');
            const errorEl = document.getElementById('searchError');
            resultEl.classList.add('hidden');
            resultEl.innerHTML = '';
            errorEl.classList.add('hidden');
            if (!q) {
                errorEl.textContent = 'Ingiza nambari ya pekee (TIPTAP-W-xxxxx).';
                errorEl.classList.remove('hidden');
                return;
            }
            resultEl.innerHTML = '<p class="text-white/50 text-sm py-3">Inatafuta…</p>';
            resultEl.classList.remove('hidden');
            fetch('{{ route("manager.waiters.search") }}?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        resultEl.classList.add('hidden');
                        resultEl.innerHTML = '';
                        errorEl.textContent = data.message || '{{ config('salon.staff') }} hajapatikana.';
                        errorEl.classList.remove('hidden');
                        return;
                    }
                    const w = data.waiter;
                    let html = '<div class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-4">';
                    html += '<div class="flex items-start gap-4">';
                    if (w.profile_photo_url) {
                        html += '<img src="' + w.profile_photo_url + '" alt="" class="w-14 h-14 rounded-xl object-cover border border-violet-500/20 shrink-0">';
                    } else {
                        html += '<div class="w-14 h-14 rounded-xl bg-gradient-to-br from-violet-500/20 to-cyan-500/20 flex items-center justify-center font-bold text-xl text-violet-400 border border-violet-500/20 shrink-0">' + (w.name ? w.name.charAt(0) : '—') + '</div>';
                    }
                    html += '<div class="min-w-0 flex-1"><p class="font-bold text-white text-lg">' + (w.name || '—') + '</p>';
                    html += '<p class="text-sm text-white/60">' + (w.email || '') + '</p>';
                    html += '<p class="text-sm text-white/60">Simu: ' + (w.phone || '—') + '</p>';
                    if (w.location) html += '<p class="text-sm text-white/60">Mahali: ' + w.location + '</p>';
                    html += '<p class="text-sm font-mono text-cyan-400 mt-2">' + (w.global_waiter_number || '') + '</p>';
                    html += '<p class="text-xs text-white/40 mt-2">{{ config('salon.booking_plural') }}: ' + (w.orders_count || 0) + ' · Ratings: ' + (w.feedback_count || 0) + '</p></div></div>';

                    if (w.work_history && w.work_history.length > 0) {
                        html += '<div class="pt-3 border-t border-white/10">';
                        html += '<p class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2">Historia ya kazi (alishafanya kazi sehemu flani, muda flani)</p>';
                        html += '<ul class="space-y-2">';
                        w.work_history.forEach(function(h) {
                            const linkedDate = h.linked_at ? new Date(h.linked_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—';
                            const unlinkedDate = h.unlinked_at ? new Date(h.unlinked_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : null;
                            const typeLabel = h.employment_type === 'temporary' ? ' (Show-time)' : ' (Muda mrefu)';
                            if (h.is_active) {
                                html += '<li class="flex items-start gap-2 text-sm"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mt-1.5 shrink-0 animate-pulse"></span><span class="text-white/80"><strong class="text-white">' + (h.restaurant_name || '—') + '</strong>' + typeLabel + ' — Anafanya kazi tangu ' + linkedDate + ' <span class="text-emerald-400 font-medium">(Active)</span></span></li>';
                            } else {
                                html += '<li class="flex items-start gap-2 text-sm"><span class="w-1.5 h-1.5 rounded-full bg-white/30 mt-1.5 shrink-0"></span><span class="text-white/70">Alifanya kazi <strong class="text-white/90">' + (h.restaurant_name || '—') + '</strong>' + typeLabel + ' — ' + linkedDate + ' hadi ' + (unlinkedDate || '—') + '</span></li>';
                            }
                        });
                        html += '</ul></div>';
                    }

                    if (w.is_linked && w.current_restaurant) {
                        html += '<p class="text-amber-400 text-sm mt-2">Tayari ameunganishwa na: ' + w.current_restaurant + '. Manager wa {{ strtolower(config('salon.entity')) }} ile anafaa kum-unlink kwanza.</p>';
                    } else {
                        var token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
                        var linkUrl = '{{ url("manager/waiters") }}/' + w.id + '/link';
                        html += '<form action="' + linkUrl + '" method="POST" class="mt-4 space-y-4">';
                        html += '<input type="hidden" name="_token" value="' + token + '">';
                        html += '<p class="text-xs text-white/40 mb-2">Chagua aina ya kuunga: muda mrefu (anabaki mpaka um-unlink) au show-time (muda maalum – weka tarehe ya mwisho).</p>';
                        html += '<div class="flex flex-wrap gap-4 items-end">';
                        html += '<div><label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-1 block">Aina ya kuunga</label>';
                        html += '<label class="inline-flex items-center gap-2 mr-4"><input type="radio" name="employment_type" value="permanent" checked class="rounded border-white/20" onchange="toggleLinkUntil(this)"> <span class="text-white text-sm">Muda mrefu (Permanent)</span></label>';
                        html += '<label class="inline-flex items-center gap-2"><input type="radio" name="employment_type" value="temporary" class="rounded border-white/20" onchange="toggleLinkUntil(this)"> <span class="text-white text-sm">Muda / Show-time</span></label></div>';
                        html += '<div id="linkUntilWrap" class="hidden"><label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-1 block">Mpaka tarehe</label>';
                        html += '<input type="date" name="linked_until" id="linkUntilInput" class="px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm" min="' + new Date().toISOString().slice(0,10) + '"></div>';
                        html += '</div>';
                        html += '<button type="submit" class="px-4 py-2 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all">Unganisha {{ config('salon.staff') }}</button>';
                        html += '</form>';
                    }
                    html += '</div>';
                    resultEl.innerHTML = html;
                    resultEl.classList.remove('hidden');
                })
                .catch(() => {
                    resultEl.classList.add('hidden');
                    resultEl.innerHTML = '';
                    errorEl.textContent = 'Hitilafu ya mtandao. Jaribu tena.';
                    errorEl.classList.remove('hidden');
                });
        }

        function openViewWaiterModal(waiter) {
            document.getElementById('viewWaiterName').textContent = waiter.name || '—';
            document.getElementById('viewWaiterEmail').textContent = waiter.email || '—';
            document.getElementById('viewWaiterCode').textContent = waiter.waiter_code || '—';
            document.getElementById('viewWaiterGlobalCode').textContent = waiter.global_waiter_number || '—';
            var photoEl = document.getElementById('viewWaiterPhoto');
            var initialEl = document.getElementById('viewWaiterInitial');
            if (waiter.profile_photo_url) {
                photoEl.src = waiter.profile_photo_url;
                photoEl.classList.remove('hidden');
                initialEl.classList.add('hidden');
            } else {
                photoEl.classList.add('hidden');
                initialEl.classList.remove('hidden');
                initialEl.textContent = (waiter.name && waiter.name.charAt(0)) || '—';
            }
            document.getElementById('viewWaiterOrders').textContent = waiter.orders_count ?? 0;
            document.getElementById('viewWaiterTips').textContent = new Intl.NumberFormat().format(waiter.tips_sum ?? 0);
            document.getElementById('viewWaiterRating').textContent = (waiter.rating ?? 0).toFixed(1) + ' ⭐';
            document.getElementById('viewWaiterRatingCount').textContent = (waiter.rating_count ?? 0) + ' reviews';
            const date = waiter.created_at ? new Date(waiter.created_at) : null;
            document.getElementById('viewWaiterJoined').textContent = date ? date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';
            var emp = (waiter.employment_type === 'temporary' && waiter.linked_until)
                ? 'Show-time · mpaka ' + new Date(waiter.linked_until).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
                : 'Muda mrefu (Permanent)';
            document.getElementById('viewWaiterEmployment').textContent = emp;
            document.getElementById('viewWaiterModal').classList.remove('hidden');
            document.getElementById('viewWaiterModal').classList.add('flex');
        }

        function closeViewWaiterModal() {
            document.getElementById('viewWaiterModal').classList.add('hidden');
            document.getElementById('viewWaiterModal').classList.remove('flex');
        }

        function toggleLinkUntil(radio) {
            var wrap = document.getElementById('linkUntilWrap');
            var input = document.getElementById('linkUntilInput');
            if (radio && radio.value === 'temporary') {
                if (wrap) wrap.classList.remove('hidden');
                if (input) input.setAttribute('required', 'required');
            } else {
                if (wrap) wrap.classList.add('hidden');
                if (input) { input.removeAttribute('required'); input.value = ''; }
            }
        }

        async function copyToClipboard(text, button) {
            try {
                await navigator.clipboard.writeText(text);
                const orig = button.innerHTML;
                button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-400"><path d="M20 6 9 17l-5-5"/></svg>';
                button.classList.add('bg-emerald-500/20', 'border-emerald-500/30');
                setTimeout(() => { button.innerHTML = orig; button.classList.remove('bg-emerald-500/20', 'border-emerald-500/30'); }, 2000);
            } catch (e) {
                alert('Copy failed');
            }
        }

        function filterWaiters() {
            const searchTerm = document.getElementById('waiterSearch').value.toLowerCase();
            const statusFilter = document.querySelector('input[name="statusFilter"]:checked')?.value || 'all';
            const sortBy = document.getElementById('sortBy').value;
            const cards = Array.from(document.querySelectorAll('.waiter-card'));
            
            let visibleCount = 0;
            cards.forEach(card => {
                const name = card.dataset.name || '';
                const code = card.dataset.code || '';
                const status = card.dataset.status || '';
                const employment = card.dataset.employment || '';
                
                let matchesSearch = !searchTerm || name.includes(searchTerm) || code.includes(searchTerm);
                let matchesStatus = statusFilter === 'all' || 
                    (statusFilter === 'online' && status === 'online') ||
                    (statusFilter === 'offline' && status === 'offline') ||
                    (statusFilter === 'permanent' && employment === 'permanent') ||
                    (statusFilter === 'temporary' && employment === 'temporary');
                
                if (matchesSearch && matchesStatus) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Sort cards
            if (sortBy === 'orders') {
                cards.sort((a, b) => parseInt(b.dataset.orders || 0) - parseInt(a.dataset.orders || 0));
            } else if (sortBy === 'name') {
                cards.sort((a, b) => (a.dataset.name || '').localeCompare(b.dataset.name || ''));
            }
            
            const grid = document.getElementById('waitersGrid');
            cards.forEach(card => grid.appendChild(card));
            
            document.getElementById('waiterCount').textContent = `(${visibleCount})`;
        }

        // Initialize count on page load
        document.addEventListener('DOMContentLoaded', function() {
            filterWaiters();
        });
    </script>
</x-manager-layout>
