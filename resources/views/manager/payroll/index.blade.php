<x-manager-layout>
    <x-slot name="header">Payroll</x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        .manager-payroll-page,
        .manager-payroll-page * {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
        }

        .manager-payroll-page h1,
        .manager-payroll-page h2,
        .manager-payroll-page h3 {
            font-weight: 700;
        }

        .manager-payroll-page .section-heading,
        .manager-payroll-page label,
        .manager-payroll-page button,
        .manager-payroll-page .status-pill,
        .manager-payroll-page .badge {
            font-weight: 500;
        }
    </style>

    <div class="manager-payroll-page">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@600;700;800&family=Figtree:wght@400;500;600;700&display=swap');

        .payroll-root { font-family: 'Figtree', sans-serif; }
        .payroll-root h1 { font-family: 'Syne', sans-serif; }
        .mono { font-family: 'DM Mono', monospace; }

        /* ── Toolbar ── */
        .toolbar { display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-bottom:20px; }
        .search-wrap { position:relative; flex:1; min-width:200px; }
        .search-wrap svg { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:rgba(255,255,255,.3); pointer-events:none; }
        .search-input {
            width:100%; padding:11px 16px 11px 42px;
            background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);
            border-radius:14px; color:#fff;
            font-family:'Figtree',sans-serif; font-size:14px;
            outline:none; transition:border-color .2s, box-shadow .2s; box-sizing:border-box;
        }
        .search-input::placeholder { color:rgba(255,255,255,.25); }
        .search-input:focus { border-color:rgba(124,58,237,.5); box-shadow:0 0 0 3px rgba(124,58,237,.1); }

        .filter-btn {
            padding:11px 16px; border-radius:14px; font-size:13px; font-weight:600;
            cursor:pointer; border:1px solid rgba(255,255,255,.1);
            background:rgba(255,255,255,.05); color:rgba(255,255,255,.5);
            transition:background .15s, border-color .15s, color .15s;
            display:flex; align-items:center; gap:7px;
        }
        .filter-btn:hover { background:rgba(255,255,255,.09); color:rgba(255,255,255,.8); }
        .filter-btn.f-all    { background:rgba(255,255,255,.12); color:#fff; border-color:rgba(255,255,255,.22); }
        .filter-btn.f-paid   { background:rgba(16,185,129,.12); color:#6ee7b7; border-color:rgba(16,185,129,.28); }
        .filter-btn.f-unpaid { background:rgba(245,158,11,.12); color:#fcd34d; border-color:rgba(245,158,11,.28); }
        .fdot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }

        /* ── Accordion ── */
        .acc-item {
            border-radius:18px; border:1px solid rgba(255,255,255,.08);
            background:rgba(255,255,255,.025); overflow:hidden;
            transition:border-color .25s, background .25s; margin-bottom:8px;
        }
        .acc-item.is-open { border-color:rgba(255,255,255,.15); background:rgba(255,255,255,.04); }
        .acc-item[data-hidden="true"] { display:none; }

        .acc-trigger {
            width:100%; display:flex; align-items:center; gap:14px;
            padding:14px 18px; cursor:pointer; background:none; border:none;
            text-align:left; color:inherit; transition:background .15s;
        }
        .acc-trigger:hover { background:rgba(255,255,255,.03); }

        .acc-avatar {
            width:42px; height:42px; border-radius:13px; flex-shrink:0;
            background:linear-gradient(135deg,rgba(124,58,237,.4),rgba(6,182,212,.4));
            border:1px solid rgba(255,255,255,.1);
            display:flex; align-items:center; justify-content:center;
            font-family:'Syne',sans-serif; font-size:17px; font-weight:800; color:#fff;
        }
        .acc-info { flex:1; min-width:0; }
        .acc-name { font-family:'Syne',sans-serif; font-size:15px; font-weight:700; color:#fff; }
        .acc-gw   { font-family:'DM Mono',monospace; font-size:11px; color:rgba(6,182,212,.7); margin-top:2px; }
        .acc-pill {
            font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
            padding:3px 9px; border-radius:20px; flex-shrink:0;
        }
        .acc-pill.paid    { background:rgba(16,185,129,.12); color:#6ee7b7; border:1px solid rgba(16,185,129,.25); }
        .acc-pill.pending { background:rgba(245,158,11,.12);  color:#fcd34d; border:1px solid rgba(245,158,11,.25); }

        .acc-net-peek { margin-left:auto; flex-shrink:0; text-align:right; }
        .acc-net-peek .plabel { font-size:10px; color:rgba(255,255,255,.35); margin-bottom:2px; }
        .acc-net-peek .pvalue { font-family:'DM Mono',monospace; font-size:14px; color:#fff; }

        .acc-chevron {
            flex-shrink:0; width:26px; height:26px; border-radius:8px;
            background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.08);
            display:flex; align-items:center; justify-content:center;
            transition:transform .25s, background .2s, border-color .2s;
        }
        .acc-item.is-open .acc-chevron { transform:rotate(180deg); background:rgba(124,58,237,.2); border-color:rgba(124,58,237,.3); }

        .acc-body { display:grid; grid-template-rows:0fr; transition:grid-template-rows .28s ease; }
        .acc-item.is-open .acc-body { grid-template-rows:1fr; }
        .acc-body-inner { overflow:hidden; }
        .acc-form-wrap { border-top:1px solid rgba(255,255,255,.06); padding:18px 18px 22px; }

        /* ── Finance panels ── */
        .fin-panel { background:rgba(0,0,0,.2); border:1px solid rgba(255,255,255,.06); border-radius:14px; padding:16px; }
        .fin-ph { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid rgba(255,255,255,.05); }
        .fin-label { font-size:10px; font-weight:700; letter-spacing:.18em; text-transform:uppercase; }
        .fin-label.e { color:#6ee7b7; } .fin-label.d { color:#f87171; }
        .fdot-glow { width:6px; height:6px; border-radius:50%; }
        .fdot-glow.e { background:#10b981; box-shadow:0 0 7px rgba(16,185,129,.7); }
        .fdot-glow.d { background:#ef4444; box-shadow:0 0 7px rgba(239,68,68,.7); }

        .field-label { display:block; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.1em; color:rgba(255,255,255,.4); margin-bottom:7px; }
        .fin-input {
            width:100%; padding:11px 14px;
            background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08);
            border-radius:11px; color:#fff;
            font-family:'DM Mono',monospace; font-size:14px;
            outline:none; transition:border-color .2s, background .2s, box-shadow .2s; box-sizing:border-box;
        }
        .fin-input::placeholder { color:rgba(255,255,255,.2); }
        .fin-input.e:focus { border-color:rgba(16,185,129,.5); background:rgba(16,185,129,.04); box-shadow:0 0 0 3px rgba(16,185,129,.08); }
        .fin-input.d:focus { border-color:rgba(239,68,68,.5); background:rgba(239,68,68,.04); box-shadow:0 0 0 3px rgba(239,68,68,.08); }
        .fin-sub { margin-top:12px; padding-top:10px; border-top:1px solid rgba(255,255,255,.05); display:flex; justify-content:space-between; align-items:center; }
        .fin-sub span:first-child { font-size:11px; color:rgba(255,255,255,.35); }

        /* ── Net summary ── */
        .net-panel { background:rgba(0,0,0,.25); border:1px solid rgba(255,255,255,.07); border-radius:14px; padding:14px 15px; }
        .net-row { display:flex; align-items:center; justify-content:space-between; }
        .net-div { height:1px; background:rgba(255,255,255,.06); margin:9px 0; }

        .btn-confirm {
            width:100%; padding:13px 14px; border-radius:13px;
            font-family:'Syne',sans-serif; font-size:13px; font-weight:700; letter-spacing:.04em;
            background:linear-gradient(135deg,#7c3aed,#0891b2); color:#fff; border:none; cursor:pointer;
            transition:transform .15s, box-shadow .2s, opacity .15s;
            box-shadow:0 4px 18px rgba(124,58,237,.28);
        }
        .btn-confirm:hover:not(:disabled) { transform:translateY(-1px); box-shadow:0 7px 24px rgba(124,58,237,.38); }
        .btn-confirm:disabled { opacity:.5; cursor:not-allowed; }
        .btn-update {
            width:100%; padding:13px 14px; border-radius:13px;
            font-family:'Syne',sans-serif; font-size:13px; font-weight:700;
            background:rgba(255,255,255,.07); color:rgba(255,255,255,.8);
            border:1px solid rgba(255,255,255,.12); cursor:pointer;
            transition:background .2s;
        }
        .btn-update:hover:not(:disabled) { background:rgba(255,255,255,.12); }

        /* ── Alerts ── */
        .alert-s { padding:13px 18px; border-radius:14px; background:rgba(16,185,129,.08); border:1px solid rgba(16,185,129,.2); color:#6ee7b7; font-size:14px; display:flex; align-items:center; gap:10px; margin-bottom:20px; }
        .alert-e { padding:13px 18px; border-radius:14px; background:rgba(239,68,68,.08); border:1px solid rgba(239,68,68,.2); color:#fca5a5; font-size:14px; margin-bottom:20px; }

        .month-select { padding:9px 14px; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); border-radius:12px; color:#fff; font-family:'Figtree',sans-serif; font-size:14px; font-weight:500; outline:none; cursor:pointer; min-width:155px; }
        .btn-history { display:inline-flex; align-items:center; gap:7px; padding:9px 15px; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); border-radius:12px; color:rgba(255,255,255,.75); font-size:13px; font-weight:600; text-decoration:none; transition:background .2s, color .2s; }
        .btn-history:hover { background:rgba(255,255,255,.1); color:#fff; }

        .stat-chip { padding:8px 14px; border-radius:12px; display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600; }
        .chip-n { background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); color:rgba(255,255,255,.5); }
        .chip-p { background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.2); color:#6ee7b7; }
        .chip-u { background:rgba(245,158,11,.1); border:1px solid rgba(245,158,11,.2); color:#fcd34d; }

        .progress-track { height:4px; border-radius:99px; background:rgba(255,255,255,.06); margin-top:14px; overflow:hidden; }
        .progress-fill  { height:100%; border-radius:99px; background:linear-gradient(90deg,#7c3aed,#10b981); transition:width .6s ease; }

        .form-grid { display:grid; grid-template-columns:1fr 1fr auto; gap:14px; align-items:start; }
        .two-col   { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
        @media(max-width:800px){ .form-grid { grid-template-columns:1fr; } .acc-net-peek { display:none; } }

        .no-results { text-align:center; padding:50px 20px; color:rgba(255,255,255,.3); font-size:14px; display:none; }
        .confirmed-badge { padding:9px 13px; border-radius:11px; background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.2); font-size:11px; color:#6ee7b7; font-family:'DM Mono',monospace; }
    </style>

    <div class="payroll-root">

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert-s">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert-e">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert-e">
                <ul style="list-style:disc;padding-left:18px;margin:0;line-height:1.9;">
                    @foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- Page header --}}
        <div style="margin-bottom:26px;">
            <div style="display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:14px;">
                <div>
                    <h1 style="font-size:28px;font-weight:800;color:#fff;letter-spacing:-.5px;margin:0 0 4px;">Payroll</h1>
                    <p style="color:rgba(255,255,255,.42);font-size:14px;margin:0;line-height:1.6;">Click {{ strtolower(config('salon.staff')) }} → fill amounts → confirm. Simple.</p>
                </div>
                <div style="display:flex;flex-wrap:wrap;align-items:center;gap:9px;">
                    <form method="GET" action="{{ route('manager.payroll.index') }}" style="display:flex;align-items:center;gap:8px;">
                        <label for="month" style="font-size:10px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:rgba(255,255,255,.32);">Month</label>
                        <select name="month" id="month" onchange="this.form.submit()" class="month-select">
                            @foreach ($months as $m)
                                <option value="{{ $m['value'] }}" {{ $m['value'] === $currentMonth ? 'selected' : '' }}>{{ $m['label'] }}</option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('manager.payroll.history') }}" class="btn-history">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        History
                    </a>
                </div>
            </div>

            @if (!$waiters->isEmpty())
                @php
                    $paidCount    = $waiters->filter(fn($w)=>$w->waiterSalaryPayments->firstWhere('period_month',$currentMonth))->count();
                    $pendingCount = $waiters->count() - $paidCount;
                    $pct          = $waiters->count() ? round($paidCount/$waiters->count()*100) : 0;
                @endphp
                <div style="margin-top:14px;display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
                    <div class="stat-chip chip-n">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        {{ $waiters->count() }} {{ strtolower(config('salon.staff_plural')) }}
                    </div>
                    <div class="stat-chip chip-p">
                        <span style="width:7px;height:7px;border-radius:50%;background:#10b981;box-shadow:0 0 6px rgba(16,185,129,.7);"></span>
                        {{ $paidCount }} paid
                    </div>
                    @if($pendingCount > 0)
                    <div class="stat-chip chip-u">
                        <span style="width:7px;height:7px;border-radius:50%;background:#f59e0b;box-shadow:0 0 6px rgba(245,158,11,.7);"></span>
                        {{ $pendingCount }} unpaid
                    </div>
                    @endif
                    <div class="stat-chip chip-n" style="margin-left:auto;">
                        <span class="mono" style="font-size:12px;">{{ $pct }}% complete</span>
                    </div>
                </div>
                <div class="progress-track"><div class="progress-fill" style="width:{{ $pct }}%;"></div></div>
            @endif
        </div>

        @if ($waiters->isEmpty())
            <div style="text-align:center;padding:72px 40px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.07);border-radius:22px;">
                <div style="width:68px;height:68px;border-radius:18px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:rgba(255,255,255,.25)"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <h3 style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#fff;margin:0 0 7px;">No linked {{ strtolower(config('salon.staff_plural')) }}</h3>
                <p style="color:rgba(255,255,255,.38);font-size:14px;max-width:300px;margin:0 auto 20px;line-height:1.6;">Link {{ strtolower(config('salon.staff_plural')) }} under {{ config('salon.staff_plural') }} &amp; team first.</p>
                <a href="{{ route('manager.waiters.index') }}" style="display:inline-flex;align-items:center;gap:7px;padding:11px 22px;background:linear-gradient(135deg,#7c3aed,#0891b2);color:#fff;border-radius:13px;font-family:'Syne',sans-serif;font-size:13px;font-weight:700;text-decoration:none;">
                    Go to {{ config('salon.staff_plural') }} &amp; team
                </a>
            </div>

        @else

            {{-- Toolbar --}}
            <div class="toolbar">
                <div class="search-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="waiter-search" class="search-input" placeholder="Search {{ strtolower(config('salon.staff')) }} name…" autocomplete="off">
                </div>
                <button class="filter-btn f-all" data-filter="all" onclick="setFilter('all')">
                    All <span class="mono" style="font-size:11px;opacity:.6;" id="cnt-all">{{ $waiters->count() }}</span>
                </button>
                <button class="filter-btn" data-filter="paid" onclick="setFilter('paid')">
                    <span class="fdot" style="background:#10b981;"></span>
                    Paid <span class="mono" style="font-size:11px;opacity:.6;" id="cnt-paid">{{ $paidCount }}</span>
                </button>
                <button class="filter-btn" data-filter="unpaid" onclick="setFilter('unpaid')">
                    <span class="fdot" style="background:#f59e0b;"></span>
                    Unpaid <span class="mono" style="font-size:11px;opacity:.6;" id="cnt-unpaid">{{ $pendingCount }}</span>
                </button>
            </div>

            {{-- Accordion list --}}
            <div id="acc-list">
                @foreach ($waiters as $waiter)
                    @php
                        $payment         = $waiter->waiterSalaryPayments->firstWhere('period_month', $currentMonth);
                        $isPaid          = (bool) $payment;
                        $basicValue      = old('user_id')==$waiter->id ? (int)old('basic_salary',0)  : (int)($payment?->basic_salary  ?? 0);
                        $allowancesValue = old('user_id')==$waiter->id ? (int)old('allowances',0)    : (int)($payment?->allowances    ?? 0);
                        $payeValue       = old('user_id')==$waiter->id ? (int)old('paye',0)           : (int)($payment?->paye           ?? 0);
                        $nssfValue       = old('user_id')==$waiter->id ? (int)old('nssf',0)           : (int)($payment?->nssf           ?? 0);
                        $netPreview      = max(($basicValue+$allowancesValue)-($payeValue+$nssfValue), 0);
                        $autoOpen        = old('user_id')==$waiter->id && $errors->any();
                    @endphp

                    <div class="acc-item {{ $autoOpen ? 'is-open' : '' }}"
                         id="acc-{{ $waiter->id }}"
                         data-name="{{ strtolower($waiter->name) }}"
                         data-status="{{ $isPaid ? 'paid' : 'unpaid' }}">

                        {{-- Trigger --}}
                        <button type="button" class="acc-trigger" onclick="toggleAcc({{ $waiter->id }})">
                            <div class="acc-avatar">{{ strtoupper(substr($waiter->name,0,1)) }}</div>
                            <div class="acc-info">
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                    <span class="acc-name">{{ $waiter->name }}</span>
                                    <span class="acc-pill {{ $isPaid ? 'paid' : 'pending' }}">{{ $isPaid ? '✓ Paid' : '○ Pending' }}</span>
                                </div>
                                <div class="acc-gw">GW-{{ $waiter->global_waiter_number ?? '—' }}</div>
                            </div>
                            <div class="acc-net-peek">
                                <div class="plabel">Net</div>
                                <div class="pvalue mono" id="peek-{{ $waiter->id }}">{{ number_format($netPreview) }}</div>
                            </div>
                            <div class="acc-chevron">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </button>

                        {{-- Body --}}
                        <div class="acc-body">
                            <div class="acc-body-inner">
                                <div class="acc-form-wrap">
                                    <form action="{{ route('manager.payroll.store') }}" method="POST"
                                          class="payroll-form" data-uid="{{ $waiter->id }}">
                                        @csrf
                                        <input type="hidden" name="user_id"      value="{{ $waiter->id }}">
                                        <input type="hidden" name="period_month" value="{{ $currentMonth }}">

                                        <div class="form-grid">

                                            {{-- Earnings --}}
                                            <div class="fin-panel">
                                                <div class="fin-ph">
                                                    <div style="display:flex;align-items:center;gap:7px;">
                                                        <span class="fdot-glow e"></span>
                                                        <span class="fin-label e">Earnings</span>
                                                    </div>
                                                    <span class="mono" style="font-size:10px;color:rgba(255,255,255,.2);">TZS</span>
                                                </div>
                                                <div class="two-col">
                                                    <div>
                                                        <label class="field-label">Basic Salary</label>
                                                        <input type="number" name="basic_salary" value="{{ $basicValue }}" min="0" step="1" placeholder="0"
                                                               class="fin-input e" data-uid="{{ $waiter->id }}" data-field="basic">
                                                    </div>
                                                    <div>
                                                        <label class="field-label">Allowances</label>
                                                        <input type="number" name="allowances" value="{{ $allowancesValue }}" min="0" step="1" placeholder="0"
                                                               class="fin-input e" data-uid="{{ $waiter->id }}" data-field="allowances">
                                                    </div>
                                                </div>
                                                <div class="fin-sub">
                                                    <span>Total</span>
                                                    <span class="mono" style="font-size:12px;color:#6ee7b7;" id="earn-{{ $waiter->id }}">{{ number_format($basicValue+$allowancesValue) }}</span>
                                                </div>
                                            </div>

                                            {{-- Deductions --}}
                                            <div class="fin-panel">
                                                <div class="fin-ph">
                                                    <div style="display:flex;align-items:center;gap:7px;">
                                                        <span class="fdot-glow d"></span>
                                                        <span class="fin-label d">Deductions</span>
                                                    </div>
                                                    <span class="mono" style="font-size:10px;color:rgba(255,255,255,.2);">TZS</span>
                                                </div>
                                                <div class="two-col">
                                                    <div>
                                                        <label class="field-label">PAYE</label>
                                                        <input type="number" name="paye" value="{{ $payeValue }}" min="0" step="1" placeholder="0"
                                                               class="fin-input d" data-uid="{{ $waiter->id }}" data-field="paye">
                                                    </div>
                                                    <div>
                                                        <label class="field-label">NSSF</label>
                                                        <input type="number" name="nssf" value="{{ $nssfValue }}" min="0" step="1" placeholder="0"
                                                               class="fin-input d" data-uid="{{ $waiter->id }}" data-field="nssf">
                                                    </div>
                                                </div>
                                                <div class="fin-sub">
                                                    <span>Total</span>
                                                    <span class="mono" style="font-size:12px;color:#f87171;" id="deduct-{{ $waiter->id }}">{{ number_format($payeValue+$nssfValue) }}</span>
                                                </div>
                                            </div>

                                            {{-- Net + CTA --}}
                                            <div style="min-width:170px;display:flex;flex-direction:column;gap:10px;">
                                                <div class="net-panel">
                                                    <p style="font-size:10px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:rgba(255,255,255,.28);margin:0 0 11px;">Summary</p>
                                                    <div class="net-row">
                                                        <span style="font-size:12px;color:rgba(255,255,255,.5);">Earnings</span>
                                                        <span class="mono" style="font-size:12px;color:#6ee7b7;" id="s-earn-{{ $waiter->id }}">{{ number_format($basicValue+$allowancesValue) }}</span>
                                                    </div>
                                                    <div class="net-row" style="margin-top:5px;">
                                                        <span style="font-size:12px;color:rgba(255,255,255,.5);">Deductions</span>
                                                        <span class="mono" style="font-size:12px;color:#f87171;" id="s-deduct-{{ $waiter->id }}">− {{ number_format($payeValue+$nssfValue) }}</span>
                                                    </div>
                                                    <div class="net-div"></div>
                                                    <div class="net-row">
                                                        <span style="font-size:13px;font-weight:700;color:#fff;">Net Pay</span>
                                                        <span class="mono" style="font-size:16px;font-weight:500;color:#fff;" id="net-{{ $waiter->id }}">{{ number_format($netPreview) }}</span>
                                                    </div>
                                                </div>

                                                @if($isPaid)
                                                <div class="confirmed-badge">
                                                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:rgba(16,185,129,.5);margin-bottom:3px;">Confirmed</div>
                                                    {{ number_format($payment->net_pay) }} TZS
                                                </div>
                                                @endif

                                                <button type="submit" class="{{ $isPaid ? 'btn-update' : 'btn-confirm' }}">
                                                    {{ $isPaid ? 'Update Payment' : 'Confirm Payment' }}
                                                </button>
                                            </div>

                                        </div>{{-- /form-grid --}}
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>{{-- /acc-item --}}
                @endforeach
            </div>

            <div class="no-results" id="no-results">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:10px;color:rgba(255,255,255,.2);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <p style="margin:0;">No matching {{ strtolower(config('salon.staff_plural')) }}.</p>
            </div>

            <p style="margin-top:14px;font-size:12px;color:rgba(255,255,255,.28);">
                Month: <strong style="color:rgba(255,255,255,.42);">{{ \Carbon\Carbon::createFromFormat('Y-m',$currentMonth)->format('F Y') }}</strong>
            </p>
        @endif
    </div>

    <script>
    (function(){
        /* ── Accordion: one open at a time ── */
        window.toggleAcc = function(uid){
            var item = document.getElementById('acc-'+uid);
            var wasOpen = item.classList.contains('is-open');
            document.querySelectorAll('.acc-item.is-open').forEach(function(el){
                el.classList.remove('is-open');
            });
            if(!wasOpen) item.classList.add('is-open');
        };

        /* ── Filter ── */
        var currentFilter = 'all';
        var currentSearch = '';

        window.setFilter = function(f){
            currentFilter = f;
            document.querySelectorAll('.filter-btn').forEach(function(btn){
                btn.classList.remove('f-all','f-paid','f-unpaid');
                if(btn.dataset.filter === f){
                    btn.classList.add(f==='all'?'f-all':f==='paid'?'f-paid':'f-unpaid');
                }
            });
            applyFilters();
        };

        document.getElementById('waiter-search')?.addEventListener('input', function(){
            currentSearch = this.value.toLowerCase().trim();
            applyFilters();
        });

        function applyFilters(){
            var items = document.querySelectorAll('.acc-item');
            var visible = 0;
            items.forEach(function(item){
                var nameOk   = item.dataset.name.includes(currentSearch);
                var statusOk = currentFilter==='all' || item.dataset.status===currentFilter;
                var show     = nameOk && statusOk;
                item.setAttribute('data-hidden', show?'false':'true');
                if(!show) item.classList.remove('is-open');
                visible += show ? 1 : 0;
            });
            var el = document.getElementById('no-results');
            if(el) el.style.display = visible===0 ? 'block' : 'none';
        }

        /* ── Live calculator ── */
        var vals = {};
        document.querySelectorAll('.fin-input').forEach(function(inp){
            inp.addEventListener('input', function(){
                var uid   = this.dataset.uid;
                var field = this.dataset.field;
                if(!vals[uid]) seedVals(uid);
                vals[uid][field] = parseInt(this.value)||0;
                recalc(uid);
            });
        });

        function seedVals(uid){
            var form = document.querySelector('.payroll-form[data-uid="'+uid+'"]');
            vals[uid] = {
                basic:      parseInt(form?.querySelector('[name="basic_salary"]')?.value)||0,
                allowances: parseInt(form?.querySelector('[name="allowances"]')?.value)||0,
                paye:       parseInt(form?.querySelector('[name="paye"]')?.value)||0,
                nssf:       parseInt(form?.querySelector('[name="nssf"]')?.value)||0,
            };
        }

        function fmt(n){ return n.toLocaleString('en-US'); }

        function recalc(uid){
            var v      = vals[uid];
            var earn   = v.basic + v.allowances;
            var deduct = v.paye  + v.nssf;
            var net    = Math.max(earn-deduct, 0);
            set('earn-'+uid,    fmt(earn));
            set('deduct-'+uid,  fmt(deduct));
            set('s-earn-'+uid,  fmt(earn));
            set('s-deduct-'+uid,'− '+fmt(deduct));
            set('net-'+uid,     fmt(net));
            set('peek-'+uid,    fmt(net));
        }
        function set(id,v){ var e=document.getElementById(id); if(e) e.textContent=v; }

        /* ── Submit: disable + auto-collapse ── */
        document.querySelectorAll('form[action="{{ route('manager.payroll.store') }}"]').forEach(function(form) {
            form.addEventListener('submit', function() {
                var btn = form.querySelector('button[type="submit"]');
                if (btn && !btn.disabled) {
                    btn.disabled = true;
                    btn.textContent = 'Processing…';
                }
                /* collapse this card after submit so on redirect it's closed */
                var uid  = form.dataset.uid;
                var card = document.getElementById('acc-'+uid);
                if(card) card.classList.remove('is-open');
            });
        });

        })();
    </script>
</div>
</x-manager-layout>