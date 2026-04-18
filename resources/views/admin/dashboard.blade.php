<x-admin-layout>
    <x-slot name="header">
        Dashboard Overview
    </x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
        <!-- Total saloons -->
        <div class="glass-card rounded-2xl p-6 card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-violet-500/20 to-violet-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-5">
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-500/20 to-purple-500/20 rounded-xl flex items-center justify-center border border-violet-500/20 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet-400">
                            <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1.5 bg-violet-500/10 text-violet-400 text-[10px] font-bold rounded-full uppercase tracking-wider border border-violet-500/20">Growth</span>
                </div>
                <p class="text-[11px] font-semibold text-white/40 uppercase tracking-wider mb-1">Total {{ config('salon.entity_plural') }}</p>
                <h3 class="text-3xl font-bold text-white tracking-tight" id="stat-total-restaurants">{{ $stats['total_restaurants'] }}</h3>
            </div>
        </div>

        <!-- Total stylists -->
        <a href="{{ route('admin.waiters.index') }}" class="glass-card rounded-2xl p-6 card-hover relative overflow-hidden group block">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-amber-500/20 to-amber-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-5">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-xl flex items-center justify-center border border-amber-500/20 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1.5 bg-amber-500/10 text-amber-400 text-[10px] font-bold rounded-full uppercase tracking-wider border border-amber-500/20">View all</span>
                </div>
                <p class="text-[11px] font-semibold text-white/40 uppercase tracking-wider mb-1">Total {{ config('salon.staff_plural') }}</p>
                <h3 class="text-3xl font-bold text-white tracking-tight" id="stat-total-waiters">{{ $stats['total_waiters'] ?? 0 }}</h3>
            </div>
        </a>

        <!-- Active bookings -->
        <div class="glass-card rounded-2xl p-6 card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-5">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-xl flex items-center justify-center border border-emerald-500/20 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-400">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1.5 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold rounded-full uppercase tracking-wider border border-emerald-500/20 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                        Live
                    </span>
                </div>
                <p class="text-[11px] font-semibold text-white/40 uppercase tracking-wider mb-1">{{ config('salon.admin_stat_active_bookings') }}</p>
                <h3 class="text-3xl font-bold text-white tracking-tight" id="stat-active-orders">{{ $stats['active_orders'] }}</h3>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="glass-card rounded-2xl p-6 card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-cyan-500/20 to-cyan-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-5">
                    <div class="w-12 h-12 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center border border-cyan-500/20 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400">
                            <rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1.5 bg-cyan-500/10 text-cyan-400 text-[10px] font-bold rounded-full uppercase tracking-wider border border-cyan-500/20">Revenue</span>
                </div>
                <p class="text-[11px] font-semibold text-white/40 uppercase tracking-wider mb-1">Total Revenue</p>
                <h3 class="text-3xl font-bold text-white tracking-tight" id="stat-total-revenue">Tsh {{ number_format($stats['total_revenue'] / 1000, 1) }}K</h3>
            </div>
        </div>

        <!-- Pending Withdrawals -->
        <div class="glass-card rounded-2xl p-6 card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-amber-500/20 to-amber-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-5">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-xl flex items-center justify-center border border-amber-500/20 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400">
                            <path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1.5 bg-amber-500/10 text-amber-400 text-[10px] font-bold rounded-full uppercase tracking-wider border border-amber-500/20">Pending</span>
                </div>
                <p class="text-[11px] font-semibold text-white/40 uppercase tracking-wider mb-1">Withdrawals</p>
                <h3 class="text-3xl font-bold text-white tracking-tight" id="stat-pending-withdrawals">{{ $stats['pending_withdrawals'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent saloons -->
        <div class="lg:col-span-2 glass-card rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-white/5 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ config('salon.admin_dashboard_newest_partners') }}</h3>
                    <p class="text-[11px] font-semibold text-violet-400 uppercase tracking-wider mt-1">Recently registered {{ config('salon.entity_plural_lower') }}</p>
                </div>
                <a href="{{ route('admin.restaurants.index') }}" class="px-4 py-2 bg-violet-500/10 text-violet-400 text-[11px] font-bold uppercase tracking-wider rounded-xl hover:bg-violet-500 hover:text-white transition-all border border-violet-500/20 hover:border-violet-500">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-white/[0.02]">
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-white/40 uppercase tracking-wider">{{ config('salon.entity') }}</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-white/40 uppercase tracking-wider">{{ config('salon.admin_table_location') }}</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-white/40 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-white/40 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($recent_restaurants as $restaurant)
                        <tr class="hover:bg-white/[0.02] transition-all group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-xl flex items-center justify-center text-violet-400 font-bold text-sm border border-violet-500/20 group-hover:scale-110 transition-transform">
                                        {{ substr($restaurant->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-semibold text-white">{{ $restaurant->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-sm font-medium text-white/60 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    {{ $restaurant->location }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1.5 {{ $restaurant->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20' }} text-[10px] font-bold rounded-full uppercase tracking-wider border">
                                    {{ $restaurant->is_active ? 'Active' : 'Blocked' }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <a href="{{ route('admin.restaurants.show', $restaurant) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-white/5 text-white/40 hover:bg-violet-500 hover:text-white transition-all border border-white/10 hover:border-violet-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-48 h-48 bg-gradient-to-br from-violet-500/10 to-cyan-500/10 rounded-full blur-3xl"></div>
            <h3 class="text-xl font-bold text-white tracking-tight mb-6 relative z-10">Recent Activity</h3>
            <div class="space-y-5 relative z-10">
                @forelse($recent_activities as $activity)
                <div class="flex gap-4 relative group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500/20 to-cyan-500/20 flex items-center justify-center shrink-0 border border-violet-500/20 group-hover:bg-gradient-to-br group-hover:from-violet-600 group-hover:to-cyan-600 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet-400 group-hover:text-white">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white leading-tight mb-1 group-hover:text-violet-400 transition-colors">{{ $activity->description }}</p>
                        <p class="text-[11px] text-white/40 font-medium uppercase tracking-wider">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-white/20 mb-4 border border-white/5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-white/60">No recent activity</p>
                    <p class="text-[11px] text-violet-400 font-medium uppercase tracking-wider mt-1">System is quiet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh stats every 30 seconds
        setInterval(function() {
            fetch('{{ route("admin.dashboard.stats") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('stat-total-restaurants').textContent = data.total_restaurants;
                    if (document.getElementById('stat-total-waiters')) document.getElementById('stat-total-waiters').textContent = data.total_waiters ?? 0;
                    document.getElementById('stat-active-orders').textContent = data.active_orders;
                    document.getElementById('stat-total-revenue').textContent = 'Tsh ' + (data.total_revenue / 1000).toFixed(1) + 'K';
                    document.getElementById('stat-pending-withdrawals').textContent = data.pending_withdrawals;
                })
                .catch(error => console.error('Error fetching stats:', error));
        }, 30000); // 30 seconds
    </script>
</x-admin-layout>
