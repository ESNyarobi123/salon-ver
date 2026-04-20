<x-guest-layout title="TIPTAP · {{ config('salon.portal_order') }} | Login" backdrop="salon-hero">
    <div class="relative">
        <div class="flex items-center gap-2 mb-2">
            <span class="inline-flex items-center rounded-full bg-red-500/15 border border-red-400/25 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-widest text-red-200/90">Service desk</span>
        </div>
        <div class="text-left mb-8">
            <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                TIPTAP · <span class="bg-gradient-to-r from-teal-200 via-white to-red-100 bg-clip-text text-transparent">{{ config('salon.portal_order') }}</span>
            </h2>
            <p class="text-white/60 font-medium mt-3 text-sm sm:text-base max-w-md leading-relaxed">
                Ingia kwa nenosiri uliyopewa na manager. {{ ucfirst(config('salon.entity')) }} yako itajulikana kiotomatiki — hakuna jina la mtumiaji.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/15 border border-emerald-400/25 text-emerald-200 text-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-500/15 border border-rose-400/25 text-rose-200 text-sm">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('order-portal.login') }}" class="space-y-6">
            @csrf

            <div class="group">
                <label for="password" class="text-[10px] font-bold uppercase tracking-wider text-teal-200/70 mb-2 block">Nenosiri la service desk</label>
                <div class="relative">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-white/35" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <input id="password" type="password" name="password" required autofocus placeholder="Ingiza PIN / nenosiri kutoka kwa manager"
                           class="block w-full pl-12 pr-4 py-4 bg-black/35 border border-white/15 rounded-xl font-medium text-white placeholder-white/35 focus:ring-2 focus:ring-red-400/50 focus:border-red-400/40 transition-all shadow-inner">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="pt-1">
                <button type="submit" class="group/btn w-full py-4 rounded-xl font-bold text-lg text-white shadow-xl shadow-red-900/35 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 transition-all duration-300 border border-red-400/25 hover:border-red-300/40 hover:shadow-red-500/20 active:scale-[0.99]">
                    <span class="inline-flex items-center justify-center gap-2">
                        Ingia kwenye {{ config('salon.live_bookings') }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="opacity-90 group-hover/btn:translate-x-0.5 transition-transform"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </span>
                </button>
            </div>

            <p class="text-center text-white/45 text-xs leading-relaxed border-t border-white/10 pt-5">
                Nenosiri peke yake linakufungulia {{ strtolower(config('salon.live_bookings')) }} za {{ strtolower(config('salon.entity')) }} yako. Unapofutwa uunganisho, nenosiri linaacha kutumika.
            </p>
        </form>
    </div>
</x-guest-layout>
