<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Salary Slip - {{ $payment->period_label }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 12px; color: #1a1a1a; max-width: 420px; margin: 24px auto; padding: 16px; -webkit-text-size-adjust: 100%; }
        .company { font-size: 11px; color: #444; margin-bottom: 8px; }
        h1 { font-size: 16px; font-weight: bold; margin: 0 0 16px 0; text-align: center; }
        .row { display: table; width: 100%; margin: 4px 0; table-layout: fixed; }
        .label { display: table-cell; }
        .amount { display: table-cell; text-align: right; font-variant-numeric: tabular-nums; word-break: break-all; }
        .sep { border-bottom: 1px dashed #999; margin: 8px 0; }
        .total { font-weight: bold; margin-top: 4px; }
        .net { font-size: 14px; font-weight: bold; margin-top: 8px; }
        .no-print { margin-top: 24px; padding: 12px; border: 1px dashed #999; border-radius: 8px; font-size: 12px; background: #f5f5f5; }
        .no-print kbd { background: #ddd; padding: 2px 6px; border-radius: 4px; }
        .no-print button { margin-top: 8px; padding: 12px 20px; min-height: 44px; background: #333; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; -webkit-tap-highlight-color: transparent; }
        .no-print button:hover { background: #555; }
        @media (max-width: 480px) { body { padding: 12px; margin: 12px auto; } h1 { font-size: 14px; } .no-print button { width: 100%; } }
        @media print { .no-print { display: none !important; } }
    </style>
    @if(!empty($autoPrint))
    <script>window.onload = function() { window.print(); };</script>
    @endif
</head>
<body>
    @if (!empty($restaurantName))
        <div class="company">{{ $restaurantName }}</div>
    @endif
    <h1>EMPLOYEE PAYSLIP - {{ $payment->period_label }}</h1>

    <div class="row">
        <span class="label">Name: {{ $waiterName }}</span>
    </div>
    <div class="row">
        <span class="label">ID: {{ $waiterId }}</span>
    </div>
    <div class="row">
        <span class="label">Position: {{ config('salon.staff') }}</span>
    </div>

    <div class="sep"></div>

    <div class="row">
        <span class="label">Basic Salary</span>
        <span class="amount">{{ number_format($payment->basic_salary) }}</span>
    </div>
    <div class="row">
        <span class="label">Allowances</span>
        <span class="amount">{{ number_format($payment->allowances) }}</span>
    </div>
    <div class="sep"></div>
    <div class="row total">
        <span class="label">Gross Salary</span>
        <span class="amount">{{ number_format($payment->gross_salary) }}</span>
    </div>

    <div class="sep"></div>
    <div class="row"><span class="label">Deductions:</span></div>
    <div class="row">
        <span class="label">PAYE</span>
        <span class="amount">{{ number_format($payment->paye) }}</span>
    </div>
    <div class="row">
        <span class="label">NSSF</span>
        <span class="amount">{{ number_format($payment->nssf) }}</span>
    </div>
    <div class="sep"></div>
    <div class="row total">
        <span class="label">Total Deduction</span>
        <span class="amount">{{ number_format($payment->total_deduction) }}</span>
    </div>

    <div class="sep"></div>
    <div class="row net">
        <span class="label">NET PAY</span>
        <span class="amount">{{ number_format($payment->net_pay) }}</span>
    </div>
    <div class="sep"></div>

    @if(empty($forPdf))
    <div class="no-print">
        <p><strong>Save as PDF:</strong> Press <kbd>Ctrl+P</kbd> (Windows) or <kbd>Cmd+P</kbd> (Mac), then choose &quot;Save as PDF&quot; or &quot;Print to PDF&quot;.</p>
        <button type="button" onclick="window.print()">Print / Save as PDF</button>
    </div>
    @endif
</body>
</html>
