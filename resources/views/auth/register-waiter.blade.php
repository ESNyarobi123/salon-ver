<x-guest-layout title="TIPTAP | {{ config('salon.staff') }} registration">
    <div class="relative">
        {{-- Header --}}
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-wider border border-cyan-500/30 mb-4">
                <span class="w-1.5 h-1.5 bg-cyan-400 rounded-full animate-pulse"></span>
                For {{ config('salon.staff_plural') }}
            </span>
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-violet-500/30 to-cyan-500/30 flex items-center justify-center border border-white/10 shadow-lg shadow-violet-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-violet-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Sajili kama {{ config('salon.staff') }}</h1>
            <p class="text-white/50 text-sm mt-2 max-w-xs mx-auto leading-relaxed">
                Pata nambari yako ya pekee. Manager atakuunga na {{ strtolower(config('salon.entity')) }} — muda mrefu au show-time.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-5 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m22 4-2.09 2.09"/></svg>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('waiter.register.store') }}" class="space-y-6">
            @csrf

            {{-- Personal --}}
            <div class="space-y-4">
                <p class="text-[10px] font-bold uppercase tracking-wider text-white/40 flex items-center gap-2">
                    <span class="w-4 h-px bg-white/20"></span>
                    Jina
                </p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="sr-only">Jina la kwanza</label>
                        <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus
                               class="block w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all text-sm"
                               placeholder="Jina la kwanza">
                        <x-input-error :messages="$errors->get('first_name')" class="mt-1.5 text-xs" />
                    </div>
                    <div>
                        <label for="last_name" class="sr-only">Jina la mwisho</label>
                        <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required
                               class="block w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all text-sm"
                               placeholder="Jina la mwisho">
                        <x-input-error :messages="$errors->get('last_name')" class="mt-1.5 text-xs" />
                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div class="space-y-4">
                <p class="text-[10px] font-bold uppercase tracking-wider text-white/40 flex items-center gap-2">
                    <span class="w-4 h-px bg-white/20"></span>
                    Wasiliana
                </p>
                <div class="space-y-4">
                    <div>
                        <label for="email" class="sr-only">Barua pepe</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="block w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all text-sm"
                               placeholder="Barua pepe">
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" />
                    </div>
                    <div>
                        <label for="phone" class="sr-only">Nambari ya simu</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required
                               class="block w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all text-sm"
                               placeholder="Nambari ya simu (0712 345 678)">
                        <x-input-error :messages="$errors->get('phone')" class="mt-1.5 text-xs" />
                    </div>
                    <div>
                        <label for="location" class="sr-only">Mahali</label>
                        <input id="location" type="text" name="location" value="{{ old('location') }}"
                               class="block w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all text-sm"
                               placeholder="Mahali (optional, mfano Dar es Salaam)">
                        <x-input-error :messages="$errors->get('location')" class="mt-1.5 text-xs" />
                    </div>
                </div>
            </div>

            {{-- Security --}}
            <div class="space-y-4">
                <p class="text-[10px] font-bold uppercase tracking-wider text-white/40 flex items-center gap-2">
                    <span class="w-4 h-px bg-white/20"></span>
                    Neno la siri
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="sr-only">Neno la siri</label>
                        <input id="password" type="password" name="password" required
                               class="block w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all text-sm"
                               placeholder="Neno la siri (min. 8)">
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs" />
                    </div>
                    <div>
                        <label for="password_confirmation" class="sr-only">Thibitisha neno la siri</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="block w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all text-sm"
                               placeholder="Thibitisha">
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl font-bold text-base shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                    <span>Fungua Akaunti</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </button>
                <p class="text-center text-white/40 text-xs mt-3">Bila malipo · Utapata code yako ya pekee mara moja</p>
            </div>

            <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-6">
                <a href="{{ route('login') }}" class="text-violet-400 font-semibold text-sm hover:text-cyan-400 transition-colors">
                    Tayari una akaunti? Ingia
                </a>
                <span class="hidden sm:inline w-px h-4 bg-white/10"></span>
                <a href="{{ route('restaurant.register') }}" class="text-white/50 text-sm hover:text-white/80 transition-colors">
                    Sajili {{ config('salon.entity') }} (Manager)
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
