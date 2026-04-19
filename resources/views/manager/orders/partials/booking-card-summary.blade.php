@php
    $tone = $tone ?? 'rose';
    $compact = $compact ?? false;
    $accents = [
        'rose' => 'bg-rose-500/20 text-rose-300 border-rose-400/25 shadow-rose-500/10',
        'amber' => 'bg-amber-500/20 text-amber-300 border-amber-400/25 shadow-amber-500/10',
        'emerald' => 'bg-emerald-500/20 text-emerald-300 border-emerald-400/25 shadow-emerald-500/10',
        'cyan' => 'bg-cyan-500/15 text-cyan-200 border-cyan-400/20 shadow-cyan-500/10',
    ];
    $chip = $accents[$tone] ?? $accents['rose'];
    $tz = config('app.timezone');
    $appt = $order->scheduled_at ? $order->scheduled_at->copy()->timezone($tz) : null;

    $createdAt = $order->created_at->copy()->timezone($tz);
    $secondsAgo = max(0, (int) $createdAt->diffInSeconds(\Carbon\Carbon::now($tz)));
    if ($secondsAgo < 1) {
        $bookedAgeBadge = 'now';
    } elseif ($secondsAgo < 60) {
        $bookedAgeBadge = $secondsAgo.'s';
    } elseif ($secondsAgo < 3600) {
        $bookedAgeBadge = intdiv($secondsAgo, 60).'m';
    } elseif ($secondsAgo < 86400) {
        $bookedAgeBadge = intdiv($secondsAgo, 3600).'h';
    } else {
        $bookedAgeBadge = intdiv($secondsAgo, 86400).'d';
    }
    $bookedAtTitle = $createdAt->format('D j M Y, H:i');
@endphp

<div class="{{ $compact ? 'space-y-2' : 'space-y-2.5' }} min-w-0 w-full">
    <div class="flex items-center justify-between gap-2 min-w-0">
        <span class="inline-flex min-w-0 flex-1 items-center gap-1.5 truncate px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border shadow-sm {{ $chip }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="opacity-80 shrink-0" aria-hidden="true">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span class="truncate">{{ config('salon.seat') }} #{{ $order->table_number }}</span>
        </span>
        <span
            class="shrink-0 inline-flex min-h-[1.75rem] min-w-[2rem] items-center justify-center rounded-md border border-white/10 bg-white/[0.06] px-2 py-0.5 text-[11px] font-bold tabular-nums tracking-tight text-white/85 shadow-sm"
            title="Booked {{ $bookedAtTitle }}"
        >{{ $bookedAgeBadge }}</span>
    </div>

    @if($appt)
        <div class="relative overflow-hidden rounded-xl border border-violet-400/25 bg-gradient-to-br from-violet-600/20 via-fuchsia-600/10 to-cyan-600/5 {{ $compact ? 'px-2.5 py-2' : 'px-3 py-2.5' }} shadow-inner shadow-violet-900/20">
            <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-violet-400/10 blur-2xl"></div>
            <div class="relative flex gap-2.5">
                <div class="flex {{ $compact ? 'h-9 w-9' : 'h-11 w-11' }} shrink-0 flex-col items-center justify-center rounded-lg bg-black/30 border border-white/10 text-violet-200">
                    <span class="text-[8px] font-bold uppercase leading-none text-violet-300/90">{{ $appt->format('M') }}</span>
                    <span class="{{ $compact ? 'text-base' : 'text-lg' }} font-bold leading-none tabular-nums text-white">{{ $appt->format('j') }}</span>
                </div>
                <div class="min-w-0 flex-1 pt-0.5">
                    <p class="text-[9px] font-bold uppercase tracking-wider text-violet-200/75">Appointment</p>
                    <p class="{{ $compact ? 'text-xs' : 'text-sm' }} font-semibold text-white tracking-tight truncate">{{ $appt->format('l, j M Y') }}</p>
                    <div class="mt-0.5 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="{{ $compact ? '12' : '14' }}" height="{{ $compact ? '12' : '14' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-cyan-300/90 shrink-0" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span class="{{ $compact ? 'text-lg' : 'text-xl' }} font-bold tabular-nums tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-violet-200 via-white to-cyan-200">{{ $appt->format('H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-white/10 bg-white/[0.04] px-3 py-2 text-center">
            <p class="text-[10px] font-medium text-white/40">No fixed slot <span class="text-white/25">·</span> walk-in</p>
        </div>
    @endif

    <div class="rounded-xl border border-white/[0.07] bg-black/25 p-2.5 space-y-2 {{ $compact ? 'text-[11px]' : '' }}">
        <div class="flex items-start gap-2.5 min-w-0">
            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white/5 text-white/40">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-[9px] font-bold uppercase tracking-wider text-white/35">{{ config('salon.customer') }}</p>
                <p class="text-xs font-semibold text-white/95 truncate">{{ $order->customer_name ?: '—' }}</p>
            </div>
        </div>
        @if($order->customer_phone)
            <div class="flex items-center gap-2.5 min-w-0 pl-[2.25rem] -mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white/30 shrink-0" aria-hidden="true">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                <a href="tel:{{ $order->customer_phone }}" class="text-xs font-medium text-cyan-300/90 hover:text-cyan-200 truncate">{{ $order->customer_phone }}</a>
            </div>
        @endif
        <div class="flex items-start gap-2.5 min-w-0 pt-0.5 border-t border-white/5">
            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white/5 text-white/40">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-[9px] font-bold uppercase tracking-wider text-white/35">{{ config('salon.staff') }}</p>
                <p class="text-xs font-semibold truncate {{ $order->waiter ? 'text-cyan-300' : 'text-white/35' }}">{{ $order->waiter?->name ?? 'Unassigned' }}</p>
            </div>
        </div>
    </div>
</div>
