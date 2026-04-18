<x-admin-layout>
    <x-slot name="header">
        System Settings
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- General Settings -->
            <div class="glass-card rounded-2xl p-8">
                <div class="mb-8">
                    <h3 class="text-2xl font-black text-white tracking-tight">General Configuration</h3>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Basic system parameters and branding</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">System Name</label>
                        <input type="text" name="system_name" value="{{ $settings['general']['system_name']->value ?? 'TIPTAP' }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Support Email</label>
                        <input type="email" name="support_email" value="{{ $settings['general']['support_email']->value ?? 'support@tiptap.com' }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    </div>
                </div>
            </div>

            <!-- Financial Settings -->
            <div class="glass-card rounded-2xl p-8">
                <div class="mb-8">
                    <h3 class="text-2xl font-black text-white tracking-tight">Financial Parameters</h3>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Commission rates and withdrawal limits</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">System Commission (%)</label>
                        <input type="number" name="commission_rate" value="{{ $settings['financial']['commission_rate']->value ?? '5' }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Min. Withdrawal (Tsh)</label>
                        <input type="number" name="min_withdrawal" value="{{ $settings['financial']['min_withdrawal']->value ?? '50000' }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    </div>
                </div>
            </div>

            <!-- Demo / Testing (Payments) -->
            <div class="glass-card rounded-2xl p-8">
                <div class="mb-8">
                    <h3 class="text-2xl font-black text-white tracking-tight">Demo / Testing</h3>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Simulate successful payments without real money (push not sent, payment marked paid)</p>
                </div>

                <div class="flex items-center gap-4">
                    <input type="hidden" name="demo_push" value="0">
                    <input type="checkbox" name="demo_push" value="1" id="demo_push"
                        {{ \App\Models\Setting::get('demo_push', '0') === '1' ? 'checked' : '' }}
                        class="w-5 h-5 rounded border-white/20 bg-white/5 text-violet-500 focus:ring-violet-500">
                    <label for="demo_push" class="text-sm font-bold text-white">Demo push ON – payments auto-success (no real push)</label>
                </div>
                <p class="text-[10px] text-amber-400/80 mt-2">When ON: USSD push is not sent; payment is marked successful. When OFF: normal Selcom flow.</p>
            </div>

            <!-- Bot & API Settings -->
            <div class="glass-card rounded-2xl p-8">
                <div class="mb-8">
                    <h3 class="text-2xl font-black text-white tracking-tight">System Automation</h3>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1">Global bot endpoints and API settings</p>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">WhatsApp Bot Number</label>
                        <input type="text" name="whatsapp_bot_number" value="{{ $settings['api']['whatsapp_bot_number']->value ?? '+255 794 321 510' }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all" placeholder="+255 000 000 000">
                        <p class="text-[10px] text-white/20">{{ config('salon.admin_settings_whatsapp_number_hint') }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Global Webhook Secret</label>
                        <input type="text" name="webhook_secret" value="{{ $settings['api']['webhook_secret']->value ?? '' }}" class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-sm font-mono text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all" placeholder="sk_live_xxxxxxxxxxxx">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                <button type="reset" class="px-8 py-4 glass text-white/60 rounded-xl font-bold text-sm hover:bg-white/10 transition-all">Reset Changes</button>
                <button type="submit" class="px-10 py-4 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-violet-500/25 transition-all">Save All Settings</button>
            </div>
        </form>
    </div>
</x-admin-layout>
