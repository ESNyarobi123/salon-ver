<x-admin-layout>
    <x-slot name="header">User Management</x-slot>

    <div class="glass-card rounded-2xl overflow-hidden border border-white/10">
        <div class="p-6 border-b border-white/5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight">System Users</h2>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Manage administrators, managers, and waiters</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="mt-6 flex flex-wrap items-end gap-4">
                <div class="relative flex-1 min-w-[200px] max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-1 block">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Name or email..."
                           class="w-full pl-10 pr-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-sm font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="absolute left-3 top-[38px] text-white/40"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                <div class="min-w-[160px]">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-1 block">Role</label>
                    <select name="role" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-sm font-medium text-white focus:ring-2 focus:ring-violet-500 [&>option]:bg-gray-900">
                        <option value="">All roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($role->name)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-violet-600 hover:bg-violet-500 text-white rounded-xl font-semibold text-sm transition-all flex items-center gap-2">Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/15 text-white rounded-xl font-semibold text-sm border border-white/10 transition-all">Clear</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[640px]">
                <thead>
                    <tr class="bg-white/5">
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">User</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Role</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">{{ config('salon.entity') }}</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-white/40 uppercase tracking-widest">Joined</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-white/40 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($users as $user)
                    <tr class="hover:bg-white/5 transition-all group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-2xl flex items-center justify-center text-violet-400 font-black text-sm border border-violet-500/20 group-hover:scale-105 transition-transform shrink-0">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-white leading-none mb-0.5">{{ $user->name }}</p>
                                    <p class="text-[10px] text-white/40 font-bold">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $role = $user->getRoleNames()->first();
                                $roleColor = match($role) {
                                    'super_admin' => 'bg-gradient-to-r from-violet-600/80 to-cyan-600/80 text-white border-0',
                                    'manager' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                    'waiter' => 'bg-orange-500/20 text-orange-400 border-orange-500/30',
                                    default => 'bg-white/10 text-white/60 border-white/20',
                                };
                            @endphp
                            <span class="px-3 py-1 {{ $roleColor }} text-[10px] font-black rounded-full uppercase tracking-widest border">{{ str_replace('_', ' ', $role ?? '—') }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-xs font-bold text-white/60">{{ $user->restaurant?->name ?? 'System' }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-xs font-medium text-white/40">{{ $user->created_at->format('M d, Y') }}</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="p-2 glass text-white/40 hover:bg-violet-600 hover:text-white rounded-xl transition-all" title="View"><i data-lucide="eye" class="w-4 h-4"></i></a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="p-2 glass text-white/40 hover:bg-violet-600 hover:text-white rounded-xl transition-all" title="Edit"><i data-lucide="edit-3" class="w-4 h-4"></i></a>
                                @if(Auth::id() !== $user->id)
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 glass text-rose-400 hover:bg-rose-500 hover:text-white rounded-xl transition-all" title="Delete"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/30"><i data-lucide="users" class="w-8 h-8"></i></div>
                                <p class="text-white font-bold">No users found</p>
                                <p class="text-sm text-white/50">Try a different search or role filter.</p>
                                <a href="{{ route('admin.users.index') }}" class="text-violet-400 hover:text-violet-300 text-sm font-semibold">Clear filters</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="p-6 border-t border-white/5">{{ $users->links() }}</div>
        @endif
    </div>
</x-admin-layout>
