<x-manager-layout>
    <x-slot name="header">
        QR & Mobile API
    </x-slot>

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-white tracking-tight">QR & Mobile API</h2>
        <p class="text-sm font-medium text-white/40 uppercase tracking-wider">Connect your {{ strtolower(config('salon.entity')) }} to the TIPTAP network</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- QR Code Generator -->
        <div class="glass-card p-8 rounded-2xl">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-xl flex items-center justify-center border border-violet-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet-400">
                        <rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white tracking-tight">{{ config('salon.seat') }} QR codes</h3>
            </div>
            
            <div class="glass p-8 rounded-xl flex flex-col items-center justify-center mb-6 border border-dashed border-white/20">
                <div class="w-40 h-40 bg-white p-3 rounded-xl shadow-xl mb-5 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" class="text-surface-900 opacity-30">
                        <rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-white/40 uppercase tracking-wider mb-5">{{ config('salon.seat') }} #05 QR code</p>
                <button class="bg-gradient-to-r from-violet-600 to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all">Download PDF Pack</button>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between p-4 glass rounded-xl">
                    <span class="font-medium text-white/70">Total {{ config('salon.seat_plural') }}</span>
                    <span class="font-bold text-white">24 {{ config('salon.seat_plural') }}</span>
                </div>
                <div class="flex items-center justify-between p-4 glass rounded-xl">
                    <span class="font-medium text-white/70">Active Scans Today</span>
                    <span class="font-bold text-white">156 Scans</span>
                </div>
            </div>
        </div>

        <!-- Selcom Payment Integration -->
        <div class="glass-card p-8 rounded-2xl">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center border border-cyan-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400">
                        <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight">Selcom Mobile Money</h3>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-white/40">USSD Push Payment Gateway</p>
                </div>
            </div>

            @if(session('success') && str_contains(session('success'), 'Selcom'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-400">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <p class="text-sm font-medium text-emerald-400">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            <form action="{{ route('manager.api.selcom.update') }}" method="POST" class="space-y-5">
                @csrf
                
                <!-- Vendor ID -->
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Vendor ID</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-4 top-1/2 -translate-y-1/2 text-white/40">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                        <input type="text" name="selcom_vendor_id" value="{{ $restaurant->selcom_vendor_id }}" placeholder="e.g., TILL60917564" 
                               class="w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all">
                    </div>
                    @error('selcom_vendor_id') <p class="text-rose-400 text-[10px] font-medium mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- API Key -->
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">API Key</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-4 top-1/2 -translate-y-1/2 text-white/40">
                            <circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/>
                        </svg>
                        <input type="password" name="selcom_api_key" value="{{ $restaurant->selcom_api_key }}" placeholder="Enter your Selcom API Key" 
                               class="w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all">
                    </div>
                    @error('selcom_api_key') <p class="text-rose-400 text-[10px] font-medium mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- API Secret -->
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">API Secret</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-4 top-1/2 -translate-y-1/2 text-white/40">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input type="password" name="selcom_api_secret" value="{{ $restaurant->selcom_api_secret }}" placeholder="Enter your Selcom API Secret" 
                               class="w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all">
                    </div>
                    @error('selcom_api_secret') <p class="text-rose-400 text-[10px] font-medium mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Live Mode Toggle -->
                <div class="flex items-center justify-between p-4 glass rounded-xl">
                    <div>
                        <p class="text-sm font-semibold text-white">Live Mode</p>
                        <p class="text-[10px] text-white/40">Enable live production payments (disable for testing)</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="selcom_is_live" value="1" {{ $restaurant->selcom_is_live ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-white/10 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-cyan-500 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-500"></div>
                    </label>
                </div>

                <!-- Status Indicator -->
                <div class="p-4 {{ $restaurant->hasSelcomConfigured() ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-amber-500/10 border-amber-500/20' }} rounded-xl border">
                    <div class="flex items-center gap-3">
                        @if($restaurant->hasSelcomConfigured())
                        <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-400">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-emerald-400">Selcom Configured</p>
                            <p class="text-[10px] text-white/40">{{ $restaurant->selcom_is_live ? 'LIVE MODE' : 'TEST MODE' }}</p>
                        </div>
                        @else
                        <div class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400">
                                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-amber-400">Not Configured</p>
                            <p class="text-[10px] text-white/40">Fill in all fields to enable payments</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="p-5 bg-cyan-500/10 rounded-xl border border-cyan-500/20">
                    <div class="flex gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-400 shrink-0">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-cyan-400 mb-1">How to get Selcom credentials?</p>
                            <p class="text-[11px] text-white/60 leading-relaxed">Log in to your Selcom Merchant Portal, go to Settings → API Integration. Copy your Vendor ID, API Key, and API Secret. Start with Test Mode, then switch to Live Mode when ready for production.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button type="submit" class="w-full bg-gradient-to-r from-cyan-600 to-blue-600 text-white py-3.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-cyan-500/25 transition-all">
                        Save Selcom Settings
                    </button>
                    
                    @if($restaurant->hasSelcomConfigured())
                    <button type="button" id="test-btn" onclick="testSelcomConnection()" class="w-full glass border border-cyan-500/30 text-cyan-400 py-3.5 rounded-xl font-semibold hover:bg-cyan-500/10 transition-all flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        Test Connection
                    </button>
                    @endif
                </div>
                
                <div id="test-result" class="hidden"></div>
            </form>
        </div>

        <!-- Customer Support Number (WhatsApp) -->
        <div class="glass-card p-8 rounded-2xl">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-xl flex items-center justify-center border border-emerald-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-400">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ config('salon.customer') }} support number</h3>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-white/40">Shown on WhatsApp bot menu</p>
                </div>
            </div>
            @if(session('success') && str_contains(session('success'), 'support'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                <p class="text-sm font-medium text-emerald-400">{{ session('success') }}</p>
            </div>
            @endif
            <form action="{{ route('manager.api.support-phone.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Phone number (e.g. 0712345678)</label>
                    <input type="text" name="support_phone" value="{{ old('support_phone', $restaurant->support_phone) }}" placeholder="0712345678 or 255712345678"
                           class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                    <p class="text-white/40 text-xs mt-1">{{ config('salon.customer') }}s will see this under "📞 Customer Support" on WhatsApp. Leave empty to hide the option or use the main {{ strtolower(config('salon.entity')) }} phone.</p>
                    @error('support_phone') <p class="text-rose-400 text-[10px] font-medium mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-3.5 px-6 rounded-xl font-semibold hover:shadow-lg hover:shadow-emerald-500/25 transition-all">
                    Save Support Number
                </button>
            </form>
        </div>

        <!-- Guest Wi‑Fi (WhatsApp bot) -->
        <div class="glass-card p-8 rounded-2xl">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-sky-500/20 to-indigo-500/20 rounded-xl flex items-center justify-center border border-sky-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-400">
                        <path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" x2="12.01" y1="20" y2="20"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight">Guest Wi‑Fi</h3>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-white/40">WiFi password option on WhatsApp bot</p>
                </div>
            </div>
            @if(session('success') && str_contains(session('success'), 'Guest Wi-Fi'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                <p class="text-sm font-medium text-emerald-400">{{ session('success') }}</p>
            </div>
            @endif
            <form action="{{ route('manager.api.guest-wifi.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Network name (SSID)</label>
                    <input type="text" name="guest_wifi_ssid" value="{{ old('guest_wifi_ssid', $restaurant->guest_wifi_ssid) }}" placeholder="e.g. SalonGuest"
                           class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all">
                    @error('guest_wifi_ssid') <p class="text-rose-400 text-[10px] font-medium mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Password</label>
                    <input type="text" name="guest_wifi_password" value="{{ old('guest_wifi_password', $restaurant->guest_wifi_password) }}" placeholder="Guest Wi‑Fi password"
                           class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all">
                    <p class="text-white/40 text-xs mt-1">Customers tap “WiFi password” on the bot to see this. Leave both empty if you do not want to share Wi‑Fi here.</p>
                    @error('guest_wifi_password') <p class="text-rose-400 text-[10px] font-medium mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-indigo-600 text-white py-3.5 px-6 rounded-xl font-semibold hover:shadow-lg hover:shadow-sky-500/25 transition-all">
                    Save Guest Wi‑Fi
                </button>
            </form>
        </div>
    </div>

    <!-- API Access -->
    <div class="glass-card p-8 rounded-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-violet-500/10 to-cyan-500/10 rounded-full blur-3xl"></div>
        
        <div class="flex items-center gap-4 mb-8 relative z-10">
            <div class="w-12 h-12 bg-gradient-to-br from-violet-500/20 to-purple-500/20 rounded-xl flex items-center justify-center border border-violet-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet-400">
                    <circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white tracking-tight">Mobile API Access</h3>
        </div>

        <p class="text-white/60 mb-8 relative z-10 leading-relaxed">Use these credentials to connect your Node.js bot or custom mobile application to the TIPTAP API.</p>

        <div class="space-y-4 relative z-10">
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">{{ config('salon.entity') }} ID</label>
                <div class="flex gap-2">
                    <input type="text" readonly value="RES-{{ Auth::user()->restaurant_id }}-TIPTAP" class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 flex-1 font-mono text-sm text-white">
                    <button class="p-3 glass rounded-xl hover:bg-white/10 transition-all text-white/60 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">API Secret Token</label>
                <div class="flex gap-2">
                    <input type="password" readonly value="••••••••••••••••••••••••" class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 flex-1 font-mono text-sm text-white">
                    <button class="p-3 glass rounded-xl hover:bg-white/10 transition-all text-white/60 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-8 p-5 bg-rose-500/10 rounded-xl border border-rose-500/20 relative z-10">
            <div class="flex gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-rose-400 shrink-0">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/>
                </svg>
                <p class="text-sm font-medium text-white/80">Keep your API keys secret. Anyone with these keys can manage your {{ strtolower(config('salon.entity')) }} bookings and service catalog.</p>
            </div>
        </div>
    </div>

    <!-- Floor display (opens existing /kitchen/display URL; token routes unchanged) -->
    <div id="manager-kds" class="glass-card p-8 rounded-2xl relative overflow-hidden mt-8 scroll-mt-24">
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-amber-500/10 to-orange-500/10 rounded-full blur-3xl"></div>
        
        <div class="flex items-center gap-4 mb-8 relative z-10">
            <div class="w-12 h-12 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-xl flex items-center justify-center border border-amber-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400">
                    <path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/>
                    <line x1="6" x2="18" y1="17" y2="17"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white tracking-tight">{{ config('salon.floor_display') }}</h3>
                <p class="text-[10px] font-bold uppercase tracking-wider text-white/40">Live {{ strtolower(config('salon.booking_plural')) }} for your front desk &amp; floor team</p>
            </div>
        </div>

        <p class="text-white/60 mb-8 relative z-10 leading-relaxed">
            Generate a secret URL your team can open on a tablet, TV, or computer to show live {{ strtolower(config('salon.booking_plural')) }}. The link stays private and only works with your unique token.
        </p>

        @if($restaurant->kitchen_token)
        <!-- Active floor display link -->
        <div class="relative z-10 space-y-4">
            <div class="p-5 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-400">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-emerald-400">{{ config('salon.floor_display') }} link active</span>
                </div>
                
                <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Secret {{ config('salon.floor_display') }} URL</label>
                <div class="flex gap-2">
                    <input type="text" readonly id="kitchen-url" value="{{ url('/kitchen/display/' . $restaurant->kitchen_token) }}" 
                           class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 flex-1 font-mono text-sm text-white truncate">
                    <button onclick="copyKitchenUrl()" class="p-3 glass rounded-xl hover:bg-white/10 transition-all text-white/60 hover:text-white" title="Copy URL">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                        </svg>
                    </button>
                    <a href="{{ url('/kitchen/display/' . $restaurant->kitchen_token) }}" target="_blank" class="p-3 glass rounded-xl hover:bg-white/10 transition-all text-white/60 hover:text-white" title="Open {{ config('salon.floor_display') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/>
                        </svg>
                    </a>
                </div>
                <p class="text-[10px] text-white/40 mt-2">Generated: {{ $restaurant->kitchen_token_generated_at ? \Carbon\Carbon::parse($restaurant->kitchen_token_generated_at)->format('M d, Y H:i') : 'Unknown' }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <form action="{{ route('manager.kitchen.generate') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3.5 glass rounded-xl font-semibold text-white/80 hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 16h5v5"/>
                        </svg>
                        Regenerate Link
                    </button>
                </form>
                
                <form action="{{ route('manager.kitchen.revoke') }}" method="POST" onsubmit="return confirm('Are you sure? This will disable the current {{ strtolower(config('salon.floor_display')) }} link.')">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3.5 bg-rose-500/10 border border-rose-500/20 rounded-xl font-semibold text-rose-400 hover:bg-rose-500/20 transition-all flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><line x1="4.93" x2="19.07" y1="4.93" y2="19.07"/>
                        </svg>
                        Revoke Access
                    </button>
                </form>
            </div>
        </div>
        @else
        <!-- No floor display link yet -->
        <div class="relative z-10">
            <div class="p-6 glass rounded-xl border border-dashed border-white/20 text-center mb-6">
                <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-white/30">
                        <path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/>
                        <line x1="6" x2="18" y1="17" y2="17"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-white mb-1">No {{ config('salon.floor_display') }} link</p>
                <p class="text-[11px] text-white/40">Generate a secret link to start using the {{ config('salon.floor_display') }}</p>
            </div>

            <form action="{{ route('manager.kitchen.generate') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3.5 rounded-xl font-semibold hover:shadow-lg hover:shadow-amber-500/25 transition-all flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                    </svg>
                    Generate {{ config('salon.floor_display') }} link
                </button>
            </form>
        </div>
        @endif

        <!-- Floor display features -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 relative z-10">
            <div class="p-4 glass rounded-xl">
                <div class="w-10 h-10 bg-violet-500/20 rounded-lg flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet-400">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-white mb-1">Real-time Updates</h4>
                <p class="text-[11px] text-white/50">{{ config('salon.booking_plural') }} appear instantly with live timers</p>
            </div>
            <div class="p-4 glass rounded-xl">
                <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-white mb-1">VIP Priority</h4>
                <p class="text-[11px] text-white/50">VIP {{ strtolower(config('salon.booking_plural')) }} highlighted for faster service</p>
            </div>
            <div class="p-4 glass rounded-xl">
                <div class="w-10 h-10 bg-rose-500/20 rounded-lg flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-rose-400">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-white mb-1">SLA Tracking</h4>
                <p class="text-[11px] text-white/50">Color-coded alerts when {{ strtolower(config('salon.booking_plural')) }} run over time</p>
            </div>
        </div>
    </div>

    <script>
        function copyKitchenUrl() {
            const input = document.getElementById('kitchen-url');
            input.select();
            navigator.clipboard.writeText(input.value);
            alert(@json(config('salon.floor_display')) + ' URL copied to clipboard!');
        }

        function testSelcomConnection() {
            const btn = document.getElementById('test-btn');
            const resultDiv = document.getElementById('test-result');
            
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Testing...';
            
            fetch('{{ route("manager.api.selcom.test") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.classList.remove('hidden');
                if (data.success) {
                    resultDiv.className = 'mt-4 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl';
                    resultDiv.innerHTML = '<div class="flex items-center gap-3"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-400"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><span class="text-sm font-medium text-emerald-400">' + data.message + '</span></div>';
                } else {
                    resultDiv.className = 'mt-4 p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl';
                    resultDiv.innerHTML = '<div class="flex items-center gap-3"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-rose-400"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg><span class="text-sm font-medium text-rose-400">' + data.message + '</span></div>';
                }
            })
            .catch(error => {
                resultDiv.classList.remove('hidden');
                resultDiv.className = 'mt-4 p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl';
                resultDiv.innerHTML = '<div class="flex items-center gap-3"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-rose-400"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg><span class="text-sm font-medium text-rose-400">Connection error. Please try again.</span></div>';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Test Connection';
            });
        }
    </script>
</x-manager-layout>
