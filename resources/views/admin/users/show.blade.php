<x-admin-layout>
    <x-slot name="header">
        User Details
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="glass-card rounded-2xl p-8">
            <div class="flex justify-between items-start mb-8">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-violet-600 to-cyan-500 rounded-2xl flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-violet-500/20">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-white tracking-tight">{{ $user->name }}</h2>
                        <p class="text-white/40 font-bold text-xs mt-1">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.users.edit', $user) }}" class="px-6 py-3 glass text-white rounded-xl font-bold text-sm hover:bg-violet-600 transition-all flex items-center gap-2">
                        <i data-lucide="edit-3" class="w-4 h-4"></i> Edit User
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 py-8 border-t border-white/10">
                <div class="space-y-8">
                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-2">Account Role</p>
                        @php
                            $role = $user->getRoleNames()->first();
                            $roleColor = match($role) {
                                'super_admin' => 'bg-gradient-to-r from-violet-600 to-cyan-600 text-white',
                                'manager' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                'waiter' => 'bg-orange-500/20 text-orange-400 border-orange-500/30',
                                default => 'bg-white/10 text-white/60 border-white/20',
                            };
                        @endphp
                        <span class="px-4 py-1.5 rounded-full {{ $roleColor }} text-[10px] font-black uppercase tracking-widest border">
                            {{ str_replace('_', ' ', $role) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-2">Associated {{ config('salon.entity') }}</p>
                        @if($user->restaurant)
                            <a href="{{ route('admin.restaurants.show', $user->restaurant) }}" class="text-white font-bold hover:text-violet-400 transition-all flex items-center gap-2">
                                {{ $user->restaurant->name }}
                                <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                        @else
                            <p class="text-white font-bold">System wide (no {{ strtolower(config('salon.entity')) }})</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-8">
                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-2">Member Since</p>
                        <p class="text-white font-bold">{{ $user->created_at->format('F d, Y') }}</p>
                        <p class="text-[10px] text-white/40 font-medium">{{ $user->created_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-2">Email Verification</p>
                        @if($user->email_verified_at)
                            <span class="flex items-center gap-2 text-emerald-400 font-bold text-sm">
                                <i data-lucide="check-circle" class="w-4 h-4"></i> Verified
                            </span>
                        @else
                            <span class="flex items-center gap-2 text-rose-400 font-bold text-sm">
                                <i data-lucide="x-circle" class="w-4 h-4"></i> Unverified
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
