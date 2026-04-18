<x-admin-layout>
    <x-slot name="header">
        {{ config('salon.entity') }} details
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-8">
            <div class="glass-card rounded-2xl p-8">
                <div class="flex justify-between items-start mb-8">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-violet-600 to-cyan-500 rounded-2xl flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-violet-500/20">
                            {{ substr($restaurant->name, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-3xl font-black text-white tracking-tight">{{ $restaurant->name }}</h2>
                            <p class="text-white/40 font-bold uppercase tracking-widest text-xs mt-1">{{ $restaurant->location ?? 'No location set' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="px-6 py-3 glass text-white rounded-xl font-bold text-sm hover:bg-violet-600 transition-all flex items-center gap-2">
                            <i data-lucide="edit-3" class="w-4 h-4"></i> Edit Details
                        </a>
                        <form action="{{ route('admin.restaurants.toggle-status', $restaurant) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-6 py-3 {{ $restaurant->is_active ? 'bg-rose-500/20 text-rose-400 border-rose-500/30' : 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' }} rounded-xl font-bold text-sm hover:opacity-80 transition-all flex items-center gap-2 border">
                                <i data-lucide="{{ $restaurant->is_active ? 'slash' : 'check-circle' }}" class="w-4 h-4"></i>
                                {{ $restaurant->is_active ? 'Block ' . config('salon.entity') : 'Unblock ' . config('salon.entity') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8 py-8 border-t border-white/10">
                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-2">Phone Number</p>
                        <p class="text-white font-bold">{{ $restaurant->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-2">Joined Date</p>
                        <p class="text-white font-bold">{{ $restaurant->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                <div class="mt-6 p-6 bg-white/5 rounded-xl border border-white/10">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-sm font-black text-white uppercase tracking-widest">Payment API Configuration</h4>
                        <span class="px-3 py-1 {{ $restaurant->hasSelcomConfigured() ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border-amber-500/30' }} text-[9px] font-black rounded-full uppercase tracking-widest border">
                            {{ $restaurant->hasSelcomConfigured() ? ($restaurant->selcom_is_live ? 'Selcom Live' : 'Selcom Test') : 'Not Configured' }}
                        </span>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mb-1">Vendor ID</p>
                            <code class="block bg-white/5 p-3 rounded-xl border border-white/10 text-xs font-mono text-white/60 truncate">
                                {{ $restaurant->selcom_vendor_id ?? 'Not Configured' }}
                            </code>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mb-1">API Key</p>
                            <code class="block bg-white/5 p-3 rounded-xl border border-white/10 text-xs font-mono text-white/60 truncate">
                                {{ $restaurant->selcom_api_key ? '••••••••' . substr($restaurant->selcom_api_key, -4) : 'Not Configured' }}
                            </code>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-white/40 uppercase tracking-widest mb-1">API Secret</p>
                            <code class="block bg-white/5 p-3 rounded-xl border border-white/10 text-xs font-mono text-white/60 truncate">
                                {{ $restaurant->selcom_api_secret ? '••••••••' . substr($restaurant->selcom_api_secret, -4) : 'Not Configured' }}
                            </code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Management -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-white/5">
                    <h3 class="text-xl font-black text-white tracking-tight">Staff Management</h3>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Managers &amp; {{ config('salon.staff_plural') }}</p>
                </div>
                
                <div class="p-6 space-y-8">
                    <!-- Managers -->
                    <div>
                        <h4 class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i data-lucide="user-check" class="w-3 h-3 text-blue-400"></i> Managers ({{ $managers->count() }})
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($managers as $manager)
                            <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl border border-white/10">
                                <div class="w-10 h-10 bg-blue-500/20 text-blue-400 rounded-xl flex items-center justify-center font-black text-xs border border-blue-500/20">
                                    {{ substr($manager->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $manager->name }}</p>
                                    <p class="text-[10px] text-white/40 font-medium">{{ $manager->email }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Stylists -->
                    <div>
                        <h4 class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i data-lucide="user" class="w-3 h-3 text-orange-400"></i> {{ config('salon.staff_plural') }} ({{ $waiters->count() }})
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($waiters as $waiter)
                            <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl border border-white/10">
                                <div class="w-10 h-10 bg-orange-500/20 text-orange-400 rounded-xl flex items-center justify-center font-black text-xs border border-orange-500/20">
                                    {{ substr($waiter->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $waiter->name }}</p>
                                    <p class="text-[10px] text-white/40 font-medium">{{ $waiter->email }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Stats -->
        <div class="space-y-8">
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-xl font-black text-white tracking-tight mb-6">Financial Overview</h3>
                <div class="space-y-4">
                    <div class="p-5 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                        <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1">Total Earnings</p>
                        <p class="text-3xl font-black text-emerald-400 tracking-tight">Tsh 1.2M</p>
                    </div>
                    <div class="p-5 bg-blue-500/10 rounded-xl border border-blue-500/20">
                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Total Orders</p>
                        <p class="text-3xl font-black text-blue-400 tracking-tight">156</p>
                    </div>
                    <div class="p-5 bg-purple-500/10 rounded-xl border border-purple-500/20">
                        <p class="text-[10px] font-black text-purple-400 uppercase tracking-widest mb-1">Avg. Rating</p>
                        <div class="flex items-center gap-2">
                            <p class="text-3xl font-black text-purple-400 tracking-tight">4.8</p>
                            <i data-lucide="star" class="w-5 h-5 text-purple-400 fill-purple-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6 border border-violet-500/20">
                <h3 class="text-xl font-black text-white tracking-tight mb-6">Quick Actions</h3>
                <div class="space-y-3">
                    <button class="w-full flex items-center gap-4 p-4 glass rounded-xl hover:bg-white/10 transition-all group">
                        <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center group-hover:scale-110 transition-all text-white/60">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-sm text-white">Email Manager</p>
                            <p class="text-[10px] text-white/40 font-medium">Send direct message</p>
                        </div>
                    </button>
                    <button class="w-full flex items-center gap-4 p-4 glass rounded-xl hover:bg-white/10 transition-all group">
                        <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center group-hover:scale-110 transition-all text-white/60">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-sm text-white">View Reports</p>
                            <p class="text-[10px] text-white/40 font-medium">Download performance</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
