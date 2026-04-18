<x-guest-layout title="TIPTAP · {{ config('salon.portal_order') }} | Login">
    <div class="relative">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-black text-white tracking-tight">TIPTAP · {{ config('salon.portal_order') }}</h2>
            <p class="text-white/50 font-medium mt-2">Service desk · Ingia kwa nenosiri uliyopewa na manager ({{ strtolower(config('salon.entity')) }} yako itajulikana kiotomatiki)</p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('order-portal.login') }}" class="space-y-6">
            @csrf

            <div class="group">
                <label for="password" class="text-[10px] font-bold uppercase tracking-wider text-white/40 mb-2 block">Nenosiri la service desk</label>
                <div class="relative">
                    <input id="password" type="password" name="password" required autofocus placeholder="Ingiza password uliyopewa na manager"
                           class="block w-full px-4 py-4 bg-white/5 border border-white/10 rounded-xl font-medium text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl font-bold text-lg shadow-xl shadow-violet-500/25 hover:shadow-violet-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    Ingia kwenye {{ config('salon.live_bookings') }}
                </button>
            </div>

            <p class="text-center text-white/40 text-xs">Nenosiri peke yake linakufungulia {{ strtolower(config('salon.live_bookings')) }} za {{ strtolower(config('salon.entity')) }} yako. Unlink unapoisha, nenosiri linaisha.</p>
        </form>
    </div>
</x-guest-layout>
