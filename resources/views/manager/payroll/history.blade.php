<x-manager-layout>
    <x-slot name="header">Payment History</x-slot>

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Payment History</h1>
                <p class="mt-1 text-white/50">All confirmed payments and salary totals for each month and year.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('manager.payroll.export') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white/5 border border-white/10 text-white/90 hover:bg-white/10 hover:text-white transition-all text-sm font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    Export CSV (all)
                </a>
                <a href="{{ route('manager.payroll.export', ['year' => now()->year]) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-white/10 bg-white/5 text-white/70 hover:bg-white/10 hover:text-white transition-all text-sm font-semibold">
                    Export {{ now()->year }}
                </a>
                <a href="{{ route('manager.payroll.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-violet-500/30 bg-violet-500/10 text-violet-300 hover:bg-violet-500/20 transition-all text-sm font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                    Back to Payroll
                </a>
            </div>
        </div>
    </div>

    @if ($byMonth->isEmpty())
        <div class="glass-card py-20 text-center rounded-2xl border border-white/10">
            <div class="w-20 h-20 mx-auto mb-5 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-white/30">
                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0 11 18 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">No payments yet</h3>
            <p class="text-white/50 max-w-sm mx-auto mb-6">Confirm payments for {{ strtolower(config('salon.staff_plural')) }} on the Payroll page, then history will appear here.</p>
            <a href="{{ route('manager.payroll.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-xl font-semibold hover:shadow-lg hover:shadow-violet-500/25 transition-all">
                Go to Payroll
            </a>
        </div>
    @else
        {{-- Summary cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="rounded-2xl p-6 border border-white/10 bg-gradient-to-br from-violet-500/10 to-cyan-500/10">
                <p class="text-[10px] font-bold uppercase tracking-wider text-white/50 mb-1">Total (Net Pay)</p>
                <p class="text-2xl font-bold text-white">{{ number_format($grandTotal) }}</p>
                <p class="text-xs text-white/40 mt-1">All payments</p>
            </div>
            @if(isset($byYear) && $byYear->isNotEmpty())
                @foreach($byYear->take(3) as $year => $data)
                    <div class="rounded-2xl p-6 border border-white/10 bg-white/5">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-white/50 mb-1">{{ $year }}</p>
                        <p class="text-xl font-bold text-white">{{ number_format($data['total_net']) }}</p>
                        <p class="text-xs text-white/40 mt-1">net · {{ number_format($data['total_gross']) }} gross</p>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- By month --}}
        <div class="space-y-6">
            @foreach ($byMonth as $period => $data)
                @php
                    $label = count(explode('-', $period)) === 2 ? \Carbon\Carbon::createFromFormat('Y-m', $period)->format('F Y') : $period;
                @endphp
                <div class="glass-card rounded-2xl border border-white/10 overflow-hidden">
                    <div class="px-6 py-4 border-b border-white/10 flex flex-wrap items-center justify-between gap-4 bg-white/5">
                        <h2 class="text-lg font-bold text-white">{{ $label }}</h2>
                        <div class="flex items-center gap-6 text-sm">
                            <span class="text-white/50">Gross <strong class="text-white font-semibold ml-1">{{ number_format($data['total_gross']) }}</strong></span>
                            <span class="text-white/50">Net <strong class="text-emerald-400 font-semibold ml-1">{{ number_format($data['total_net']) }}</strong></span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left min-w-[640px]">
                            <thead>
                                <tr class="border-b border-white/10 bg-white/5">
                                    <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-white/50">{{ config('salon.staff') }}</th>
                                    <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-white/50">ID</th>
                                    <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-white/50 text-right">Basic</th>
                                    <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-white/50 text-right">Allowances</th>
                                    <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-white/50 text-right">PAYE</th>
                                    <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-white/50 text-right">NSSF</th>
                                    <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-white/50 text-right">Net Pay</th>
                                    <th class="px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-white/50">Paid At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['payments'] as $p)
                                    <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                        <td class="px-5 py-3.5 font-medium text-white">{{ $p->user?->name ?? '—' }}</td>
                                        <td class="px-5 py-3.5 font-mono text-sm text-cyan-400">{{ $p->user?->global_waiter_number ?? '—' }}</td>
                                        <td class="px-5 py-3.5 text-right text-white/80 tabular-nums">{{ number_format($p->basic_salary) }}</td>
                                        <td class="px-5 py-3.5 text-right text-white/80 tabular-nums">{{ number_format($p->allowances) }}</td>
                                        <td class="px-5 py-3.5 text-right text-white/80 tabular-nums">{{ number_format($p->paye) }}</td>
                                        <td class="px-5 py-3.5 text-right text-white/80 tabular-nums">{{ number_format($p->nssf) }}</td>
                                        <td class="px-5 py-3.5 text-right font-semibold text-emerald-400 tabular-nums">{{ number_format($p->net_pay) }}</td>
                                        <td class="px-5 py-3.5 text-sm text-white/60">{{ $p->paid_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-manager-layout>
