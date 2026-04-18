<x-admin-layout>
    <x-slot name="header">{{ config('salon.entity') }} partners</x-slot>

    <div class="glass-card rounded-2xl overflow-hidden border border-white/10">
        <div class="p-6 border-b border-white/5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight">All {{ config('salon.entity_plural') }}</h2>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Manage and monitor your salon network</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.restaurants.index') }}" class="mt-6 flex flex-wrap items-end gap-4">
                <div class="relative flex-1 min-w-[200px] max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-1 block">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, location, or phone..."
                           class="w-full pl-10 pr-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-sm font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="absolute left-3 top-[38px] text-white/40"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                <div class="min-w-[140px]">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-1 block">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-sm font-medium text-white focus:ring-2 focus:ring-violet-500 [&>option]:bg-gray-900">
                        <option value="">All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-violet-600 hover:bg-violet-500 text-white rounded-xl font-semibold text-sm transition-all">Filter</button>
                    <a href="{{ route('admin.restaurants.index') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/15 text-white rounded-xl font-semibold text-sm border border-white/10 transition-all">Clear</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[640px]">
                <thead>
                    <tr class="bg-white/5">
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">{{ config('salon.entity') }}</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Location</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Staff</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-white/40 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($restaurants as $restaurant)
                    <tr class="hover:bg-white/5 transition-all group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-2xl flex items-center justify-center text-violet-400 font-black text-sm border border-violet-500/20 group-hover:scale-105 transition-transform shrink-0">
                                    {{ substr($restaurant->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-white leading-none mb-0.5">{{ $restaurant->name }}</p>
                                    <p class="text-[10px] text-white/40 font-bold uppercase tracking-widest">ID #{{ str_pad($restaurant->id, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-xs font-bold text-white/60">{{ $restaurant->location ?? '—' }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex gap-4">
                                <div>
                                    <span class="text-[10px] font-black text-white block">{{ $restaurant->users_count ?? 0 }}</span>
                                    <span class="text-[8px] font-bold text-white/40 uppercase tracking-widest">Managers</span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-black text-white block">{{ $restaurant->waiters_count ?? 0 }}</span>
                                    <span class="text-[8px] font-bold text-white/40 uppercase tracking-widest">{{ config('salon.staff_plural') }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @if($restaurant->is_active)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/20 text-emerald-400 text-[10px] font-black rounded-full uppercase tracking-widest border border-emerald-500/30">
                                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> Active
                            </span>
                            @else
                            <span class="px-3 py-1.5 bg-rose-500/20 text-rose-400 text-[10px] font-black rounded-full uppercase tracking-widest border border-rose-500/30">Blocked</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.restaurants.show', $restaurant) }}" class="p-2 glass text-white/40 hover:bg-violet-600 hover:text-white rounded-xl transition-all" title="View"><i data-lucide="eye" class="w-4 h-4"></i></a>
                                <a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="p-2 glass text-white/40 hover:bg-violet-600 hover:text-white rounded-xl transition-all" title="Edit"><i data-lucide="edit-3" class="w-4 h-4"></i></a>
                                <form action="{{ route('admin.restaurants.toggle-status', $restaurant) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 {{ $restaurant->is_active ? 'glass text-rose-400 hover:bg-rose-500' : 'glass text-emerald-400 hover:bg-emerald-500' }} hover:text-white rounded-xl transition-all" title="{{ $restaurant->is_active ? 'Block' : 'Unblock' }}">
                                        <i data-lucide="{{ $restaurant->is_active ? 'slash' : 'check' }}" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/30"><i data-lucide="store" class="w-8 h-8"></i></div>
                                <p class="text-white font-bold">No {{ config('salon.entity_plural_lower') }} found</p>
                                <p class="text-sm text-white/50">Try a different search or status filter.</p>
                                <a href="{{ route('admin.restaurants.index') }}" class="text-violet-400 hover:text-violet-300 text-sm font-semibold">Clear filters</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($restaurants->hasPages())
        <div class="p-6 border-t border-white/5">{{ $restaurants->links() }}</div>
        @endif
    </div>
</x-admin-layout>
