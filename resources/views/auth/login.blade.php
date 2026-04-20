<x-guest-layout title="TIPTAP | Sign in" :showTopLogo="false" backdrop="salon-hero">
    <div class="relative">
        <div class="text-left sm:text-center mb-6 sm:mb-8">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-black/30 border border-white/15 flex items-center justify-center shadow-lg shadow-red-900/20 overflow-hidden">
                <img src="{{ asset('logo.jpeg') }}" alt="TIPTAP" class="w-full h-full object-cover">
            </div>
            <h2 class="mt-4 text-2xl sm:text-3xl font-black text-white tracking-tight">
                Karibu <span class="bg-gradient-to-r from-teal-200 via-white to-red-100 bg-clip-text text-transparent">tena</span>
            </h2>
            <p class="text-white/60 font-medium mt-2 text-sm sm:text-base max-w-md mx-auto leading-relaxed">Ingia kwenye saloon workspace yako uendelee na bookings na mauzo.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-6" novalidate>
            @csrf

            <!-- Email Address -->
            <div class="group">
                <label for="email" class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-teal-200/70 mb-1.5 sm:mb-2 block">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-white/30 group-focus-within:text-teal-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com"
                           class="block w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 bg-black/35 border border-white/15 rounded-xl font-medium text-white text-sm sm:text-base placeholder-white/35 focus:ring-2 focus:ring-red-400/45 focus:border-red-400/35 transition-all hover:border-white/25 shadow-inner">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="group">
                <div class="flex justify-between items-center mb-1.5 sm:mb-2">
                    <label for="password" class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-teal-200/70">Password</label>
                    @if (Route::has('password.request'))
                        <a class="text-[10px] sm:text-xs font-bold text-red-300/90 hover:text-teal-300 transition-colors" href="{{ route('password.request') }}">
                            Forgot Password?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-white/30 group-focus-within:text-red-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                           class="block w-full pl-10 sm:pl-12 pr-12 sm:pr-14 py-3 sm:py-4 bg-black/35 border border-white/15 rounded-xl font-medium text-white text-sm sm:text-base placeholder-white/35 focus:ring-2 focus:ring-red-400/45 focus:border-red-400/35 transition-all hover:border-white/25 shadow-inner">
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 px-3 sm:px-4 text-white/40 hover:text-white transition-colors" aria-label="Show password" title="Show/Hide">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                <p id="capsHint" class="mt-2 text-[11px] font-semibold text-amber-200/90 hidden">
                    Caps Lock iko ON
                </p>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between gap-3">
                <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                    <input id="remember_me" type="checkbox" class="w-4 h-4 sm:w-5 sm:h-5 rounded bg-white/5 border-white/20 text-red-500 focus:ring-red-400/50 focus:ring-offset-0 transition-all" name="remember">
                    <span class="ms-2 sm:ms-3 text-xs sm:text-sm font-medium text-white/60">Nikumbuke</span>
                </label>
                <div class="text-[10px] sm:text-xs text-white/40 font-semibold hidden sm:block">
                    Tip: tumia password ya manager/stylist
                </div>
            </div>

            <!-- Sign In Button -->
            <div class="pt-1 sm:pt-2">
                <button type="submit" class="w-full py-3 sm:py-4 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white rounded-xl font-bold text-base sm:text-lg shadow-xl shadow-red-900/35 border border-red-400/25 hover:border-red-300/40 hover:shadow-red-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <span>Ingia</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>

            <!-- Register Links -->
            <!-- <div class="text-center pt-3 sm:pt-4 space-y-2">
                <p class="text-white/40 font-medium text-xs sm:text-sm">Don't have an account yet?</p>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 justify-center">
                    <a href="{{ route('restaurant.register') }}" class="text-violet-400 font-bold hover:text-cyan-400 transition-colors text-xs sm:text-sm flex items-center justify-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Register {{ config('salon.entity') }}
                    </a>
                    <span class="hidden sm:inline text-white/20">|</span>
                    <a href="{{ route('waiter.register') }}" class="text-cyan-400 font-bold hover:text-violet-400 transition-colors text-xs sm:text-sm flex items-center justify-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Register as Waiter
                    </a>
                </div>
            </div> -->
        </form>

        <script>
            (function () {
                const pwd = document.getElementById('password');
                const btn = document.getElementById('togglePassword');
                const caps = document.getElementById('capsHint');
                if (!pwd || !btn) return;

                btn.addEventListener('click', function () {
                    const isHidden = pwd.type === 'password';
                    pwd.type = isHidden ? 'text' : 'password';
                    btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                });

                const updateCaps = (e) => {
                    if (!caps) return;
                    const on = e && typeof e.getModifierState === 'function' ? e.getModifierState('CapsLock') : false;
                    caps.classList.toggle('hidden', !on);
                };
                pwd.addEventListener('keyup', updateCaps);
                pwd.addEventListener('keydown', updateCaps);
            })();
        </script>
    </div>
</x-guest-layout>
