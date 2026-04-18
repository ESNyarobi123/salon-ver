<x-admin-layout>
    <x-slot name="header">
        Edit {{ config('salon.entity') }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="glass-card rounded-2xl p-8">
            <div class="mb-8">
                <h3 class="text-2xl font-black text-white tracking-tight">{{ config('salon.entity') }} settings</h3>
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Update restaurant profile and configuration</p>
            </div>

            <form action="{{ route('admin.restaurants.update', $restaurant) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">{{ config('salon.entity') }} name</label>
                        <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all" required>
                        @error('name') <p class="text-rose-400 text-[10px] font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $restaurant->phone) }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                        @error('phone') <p class="text-rose-400 text-[10px] font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">Location / Address</label>
                    <input type="text" name="location" value="{{ old('location', $restaurant->location) }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    @error('location') <p class="text-rose-400 text-[10px] font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-6 border-t border-white/10">
                    <div class="mb-6">
                        <h4 class="text-sm font-black text-white uppercase tracking-widest">Payment Gateway (Selcom)</h4>
                        <p class="text-[10px] text-white/40 font-bold mt-1">Configure Selcom credentials for processing payments</p>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">Vendor ID</label>
                            <input type="text" name="selcom_vendor_id" value="{{ old('selcom_vendor_id', $restaurant->selcom_vendor_id) }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-mono text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all" placeholder="e.g., TILL60917564">
                            @error('selcom_vendor_id') <p class="text-rose-400 text-[10px] font-bold mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">API Key</label>
                            <input type="text" name="selcom_api_key" value="{{ old('selcom_api_key', $restaurant->selcom_api_key) }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-mono text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all" placeholder="Enter API Key">
                            @error('selcom_api_key') <p class="text-rose-400 text-[10px] font-bold mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 block">API Secret</label>
                            <input type="password" name="selcom_api_secret" value="{{ old('selcom_api_secret', $restaurant->selcom_api_secret) }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-mono text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all" placeholder="Enter API Secret">
                            @error('selcom_api_secret') <p class="text-rose-400 text-[10px] font-bold mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-white/5 rounded-xl border border-white/10">
                            <div>
                                <p class="text-sm font-semibold text-white">Live Mode</p>
                                <p class="text-[10px] text-white/40">Enable for production payments</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="selcom_is_live" value="1" {{ old('selcom_is_live', $restaurant->selcom_is_live) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-white/10 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-violet-500 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6">
                    <a href="{{ route('admin.restaurants.show', $restaurant) }}" class="px-8 py-4 glass text-white/60 rounded-xl font-bold text-sm hover:bg-white/10 transition-all">Cancel</a>
                    <button type="submit" class="px-8 py-4 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-violet-500/25 transition-all">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
