{{-- Hub shortcuts: every manager route (see DashboardController::buildManagerQuickLinks) --}}
<div class="glass-card rounded-2xl p-6 md:p-8 mb-10 border border-white/10">
    <h3 class="text-xl font-bold text-white tracking-tight mb-1">{{ config('salon.manager_dash_hub_title') }}</h3>
    <p class="text-sm text-white/45 mb-6 max-w-3xl">{{ config('salon.manager_dash_hub_subtitle') }}</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-3">
        @foreach ($managerQuickLinks as $link)
            <a
                href="{{ $link['href'] }}"
                @if (! empty($link['external'])) target="_blank" rel="noopener noreferrer" @endif
                class="group glass rounded-xl px-4 py-3.5 border border-white/10 hover:border-violet-500/30 hover:bg-white/[0.04] transition-all flex items-start justify-between gap-3 card-hover"
            >
                <div class="min-w-0 text-left">
                    <p class="font-semibold text-white text-sm leading-snug group-hover:text-violet-200 transition-colors">{{ $link['title'] }}</p>
                    @if (! empty($link['subtitle']))
                        <p class="text-[11px] text-white/40 mt-1 leading-snug">{{ $link['subtitle'] }}</p>
                    @endif
                </div>
                <span class="shrink-0 text-white/25 group-hover:text-violet-400 transition-colors" aria-hidden="true">
                    @if (! empty($link['external']))
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/>
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    @endif
                </span>
            </a>
        @endforeach
    </div>
    <p class="text-[11px] text-white/35 mt-5">
        {{ config('salon.floor_display') }}:
        <a href="{{ route('manager.api.index') }}#manager-kds" class="text-violet-400 hover:text-violet-300 font-semibold">{{ config('salon.manager_kds_configure_hint') }}</a>
    </p>
</div>
