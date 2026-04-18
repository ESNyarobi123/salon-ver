<x-waiter-layout>
    <x-slot name="header">History</x-slot>

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-white tracking-tight">Historia yangu ya {{ config('salon.entity_plural') }}</h2>
        <p class="text-white/50 font-medium mt-1">{{ config('salon.entity_plural') }} ulizofanya kazi na muda uliounganishwa na kutolewa.</p>
    </div>

    @if($assignments->isEmpty())
        <div class="glass-card py-16 text-center rounded-2xl border border-white/10">
            <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/5">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white/20">
                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0 11 18 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Bado hakuna historia</h3>
            <p class="text-white/40">{{ config('salon.stylist_history_empty_hint_sw') }}</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($assignments as $a)
                <div class="glass-card rounded-2xl p-6 border border-white/10 hover:border-white/20 transition-colors">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h3 class="text-lg font-bold text-white truncate">{{ $a->restaurant?->name ?? config('salon.entity') }}</h3>
                            @if($a->restaurant?->location)
                                <p class="text-sm text-white/50 mt-1 flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    {{ $a->restaurant->location }}
                                </p>
                            @endif
                            @if($a->restaurant?->phone)
                                <p class="text-sm text-white/40 mt-0.5">{{ $a->restaurant->phone }}</p>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-2 shrink-0">
                            @if($a->employment_type === 'temporary')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-amber-500/20 text-amber-400 border border-amber-500/30">Show-time</span>
                                @if($a->linked_until)
                                    <span class="text-white/40 text-xs">mpaka {{ $a->linked_until->format('d/m/Y') }}</span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-white/10 text-white/60 border border-white/10">Muda mrefu</span>
                            @endif
                            @if($a->unlinked_at)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-white/5 text-white/50 border border-white/10">Imetolewa</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                    Active
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-white/5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="text-white/40 uppercase tracking-wider text-[10px] font-bold">Alilingwa</span>
                            <span class="text-white/80 font-medium">{{ $a->linked_at?->format('d/m/Y H:i') ?? '—' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-white/40 uppercase tracking-wider text-[10px] font-bold">Alitolewa</span>
                            @if($a->unlinked_at)
                                <span class="text-white/80 font-medium">{{ $a->unlinked_at->format('d/m/Y H:i') }}</span>
                            @else
                                <span class="text-emerald-400 font-medium">— Sasa (active)</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-waiter-layout>
