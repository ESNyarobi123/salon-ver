<x-admin-layout>
    <x-slot name="header">
        Edit User
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="glass-card rounded-2xl p-8">
            <div class="mb-8">
                <h3 class="text-2xl font-black text-white tracking-tight">User Permissions</h3>
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Update user profile and access levels</p>
            </div>

            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all" required>
                        @error('name') <p class="text-rose-400 text-[10px] font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all" required>
                        @error('email') <p class="text-rose-400 text-[10px] font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">System Role</label>
                        <select name="role" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all [&>option]:text-black">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role') <p class="text-rose-400 text-[10px] font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">Assigned {{ config('salon.entity') }}</label>
                        <select name="restaurant_id" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all [&>option]:text-black">
                            <option value="">None (System Admin)</option>
                            @foreach($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}" {{ $user->restaurant_id == $restaurant->id ? 'selected' : '' }}>
                                    {{ $restaurant->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('restaurant_id') <p class="text-rose-400 text-[10px] font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6">
                    <a href="{{ route('admin.users.index') }}" class="px-8 py-4 glass text-white/60 rounded-xl font-bold text-sm hover:bg-white/10 transition-all">Cancel</a>
                    <button type="submit" class="px-8 py-4 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-violet-500/25 transition-all">Update User</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
