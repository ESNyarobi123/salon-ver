<x-waiter-layout>
    <x-slot name="header">{{ config('salon.stylist_not_linked_page_title') }}</x-slot>

    <div class="max-w-xl mx-auto py-8">
        <div class="glass-card rounded-2xl p-8 text-center border border-white/10">
            <div class="w-20 h-20 bg-gradient-to-br from-violet-500/20 to-cyan-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-violet-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-violet-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Hujaunganishwa na {{ config('salon.entity') }}</h2>
            <p class="text-white/60 mb-6">Manager wa {{ strtolower(config('salon.entity')) }} atakuunga kwa kutumia nambari yako ya pekee. Baada ya kuungwa utaona dashboard yako na QR code.</p>

            <div class="bg-white/5 rounded-xl p-6 border border-white/10 mb-6">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-2">Nambari yako ya pekee ({{ config('salon.staff') }} code)</p>
                <p class="text-2xl font-mono font-bold text-cyan-400 tracking-wide">{{ Auth::user()->global_waiter_number }}</p>
                <p class="text-xs text-white/40 mt-2">Toa hii nambari kwa manager ili akuunge na {{ strtolower(config('salon.entity')) }} yake.</p>
                <button type="button" onclick="navigator.clipboard.writeText('{{ Auth::user()->global_waiter_number }}'); this.textContent=@json(config('salon.stylist_not_linked_copied')); this.classList.add('!bg-emerald-600'); setTimeout(() => { this.textContent=@json(config('salon.stylist_not_linked_copy')); this.classList.remove('!bg-emerald-600'); }, 2000)"
                        class="mt-4 px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold rounded-xl transition-all duration-200">
                    {{ config('salon.stylist_not_linked_copy') }}
                </button>
            </div>

            <div class="text-left bg-white/5 rounded-xl p-4 border border-white/5 text-sm text-white/70">
                <p class="font-semibold text-white/90 mb-2">Hatua zinazofuata:</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Nenda kwa manager wa {{ strtolower(config('salon.entity')) }} unayotaka kufanya kazi.</li>
                    <li>Mpa nambari yako ya pekee: <strong class="text-cyan-400">{{ Auth::user()->global_waiter_number }}</strong></li>
                    <li>Manager atakuunga kwenye mfumo wake (Tafuta → Unganisha {{ strtolower(config('salon.staff')) }}).</li>
                    <li>Onda ukurasa huu (F5) au ingia tena – utaona dashboard yako na QR code.</li>
                </ol>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button type="submit" class="text-white/50 hover:text-rose-400 text-sm font-medium transition-colors">
                    Ondoka (Logout)
                </button>
            </form>
        </div>
    </div>
</x-waiter-layout>
