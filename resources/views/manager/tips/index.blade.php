<x-manager-layout>
    <x-slot name="header">
        Tips Tracking
    </x-slot>

    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">Tips Tracking</h2>
            <p class="text-sm font-medium text-white/40 uppercase tracking-wider">Monitor and distribute staff tips</p>
        </div>
        <div class="flex gap-3">
            <button class="glass px-5 py-3 rounded-xl font-semibold text-white/70 hover:text-white hover:bg-white/10 transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>
                </svg>
                This Month
            </button>
            <button class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>
                </svg>
                Distribute Tips
            </button>
        </div>
    </div>

    <!-- Tips Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-amber-500/20 to-amber-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-xl flex items-center justify-center mb-5 border border-amber-500/20 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400">
                        <circle cx="8" cy="8" r="6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18"/><path d="M7 6h1v4"/><path d="m16.71 13.88.7.71-2.82 2.82"/>
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-white/40 uppercase tracking-wider mb-1">Total Tips Today</p>
                <h3 class="text-3xl font-bold text-white tracking-tight">Tsh {{ number_format($totalTipsToday) }}</h3>
            </div>
        </div>

        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-cyan-500/20 to-cyan-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center mb-5 border border-cyan-500/20 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/>
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-white/40 uppercase tracking-wider mb-1">Avg. tip per booking</p>
                <h3 class="text-3xl font-bold text-white tracking-tight">Tsh {{ number_format($avgTip) }}</h3>
            </div>
        </div>

        <div class="glass-card p-6 rounded-2xl card-hover relative overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-xl flex items-center justify-center mb-5 border border-emerald-500/20 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-400">
                        <circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-white/40 uppercase tracking-wider mb-1">Top {{ strtolower(config('salon.staff')) }} today</p>
                <h3 class="text-3xl font-bold text-white tracking-tight">{{ $topWaiter->name ?? 'None' }}</h3>
            </div>
        </div>
    </div>

    <!-- Staff tip table -->
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-white/5 flex justify-between items-center">
            <h3 class="text-xl font-bold text-white tracking-tight">Staff Tip Performance</h3>
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-white/40">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="text" placeholder="Search staff..." class="pl-10 pr-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-sm font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/[0.02]">
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Staff Member</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Total bookings</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Total Tips</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-white/40 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($waiterPerformance as $waiter)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-xl flex items-center justify-center font-bold text-violet-400 border border-violet-500/20 group-hover:scale-110 transition-transform">
                                        {{ substr($waiter->name, 0, 1) }}
                                    </div>
                                    <span class="font-semibold text-white">{{ $waiter->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 font-medium text-white/60">{{ $waiter->orders_count }} Orders</td>
                            <td class="px-6 py-5 font-bold text-white">Tsh {{ number_format($waiter->tips_sum_amount ?? 0) }}</td>
                            <td class="px-6 py-5">
                                <button class="text-[11px] font-bold uppercase tracking-wider text-violet-400 hover:text-violet-300 transition-colors">View Details</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-white/40 font-medium">No staff data found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-manager-layout>
