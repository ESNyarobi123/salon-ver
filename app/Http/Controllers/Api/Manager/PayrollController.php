<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StorePayrollPaymentRequest;
use App\Models\User;
use App\Models\WaiterSalaryPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollController extends Controller
{
    /**
     * Payroll index: waiters list with optional month filter.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', WaiterSalaryPayment::class);

        $restaurantId = Auth::user()->restaurant_id;
        $requested = $request->string('month')->trim();
        $currentMonth = preg_match('/^\d{4}-\d{2}$/', $requested) ? $requested : now()->format('Y-m');

        $waiters = User::role('waiter')
            ->activeAtRestaurant($restaurantId)
            ->with(['waiterSalaryPayments' => fn ($q) => $q->where('restaurant_id', $restaurantId)])
            ->orderBy('name')
            ->get()
            ->map(function ($waiter) use ($currentMonth) {
                $payment = $waiter->waiterSalaryPayments->firstWhere('period_month', $currentMonth);

                return [
                    'id' => $waiter->id,
                    'name' => $waiter->name,
                    'global_waiter_number' => $waiter->global_waiter_number,
                    'period_month' => $currentMonth,
                    'payment' => $payment ? [
                        'basic_salary' => (float) $payment->basic_salary,
                        'allowances' => (float) $payment->allowances,
                        'paye' => (float) $payment->paye,
                        'nssf' => (float) $payment->nssf,
                        'net_pay' => (float) $payment->net_pay,
                        'paid_at' => $payment->paid_at?->toIso8601String(),
                    ] : null,
                ];
            });

        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $d = now()->subMonths($i);
            $months[] = ['value' => $d->format('Y-m'), 'label' => $d->format('M Y')];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'current_month' => $currentMonth,
                'months' => $months,
                'waiters' => $waiters,
            ],
        ]);
    }

    /**
     * Confirm payment (store/update).
     */
    public function store(StorePayrollPaymentRequest $request): JsonResponse
    {
        $this->authorize('create', WaiterSalaryPayment::class);

        $restaurantId = Auth::user()->restaurant_id;
        $waiter = User::role('waiter')->where('id', $request->user_id)->where('restaurant_id', $restaurantId)->firstOrFail();

        $basic = (float) $request->basic_salary;
        $allowances = (float) $request->allowances;
        $paye = (float) $request->paye;
        $nssf = (float) $request->nssf;
        $netPay = $basic + $allowances - $paye - $nssf;

        $payment = WaiterSalaryPayment::updateOrCreate(
            [
                'restaurant_id' => $restaurantId,
                'user_id' => $waiter->id,
                'period_month' => $request->period_month,
            ],
            [
                'basic_salary' => $basic,
                'allowances' => $allowances,
                'paye' => $paye,
                'nssf' => $nssf,
                'net_pay' => $netPay,
                'paid_at' => now(),
                'confirmed_by' => Auth::id(),
            ]
        );

        $waiter->notify(new \App\Notifications\SalaryPaymentConfirmed($payment));

        return response()->json([
            'success' => true,
            'message' => 'Malipo yamethibitishwa kwa '.$waiter->name.'.',
            'data' => [
                'period_month' => $payment->period_month,
                'net_pay' => (float) $payment->net_pay,
            ],
        ], 201);
    }

    /**
     * Payment history (grouped by month, with yearly totals).
     */
    public function history(Request $request): JsonResponse
    {
        $this->authorize('viewAny', WaiterSalaryPayment::class);

        $restaurantId = Auth::user()->restaurant_id;
        $query = WaiterSalaryPayment::query()
            ->where('restaurant_id', $restaurantId)
            ->with(['user:id,name,global_waiter_number'])
            ->orderByDesc('period_month')
            ->orderBy('user_id');

        $year = $request->string('year')->trim();
        if (preg_match('/^\d{4}$/', $year)) {
            $query->whereRaw('LEFT(period_month, 4) = ?', [$year]);
        }

        $payments = $query->get();

        $byMonth = $payments->groupBy('period_month')->map(function ($items) {
            return [
                'payments' => $items->map(fn ($p) => [
                    'waiter_name' => $p->user?->name,
                    'waiter_id' => $p->user?->global_waiter_number,
                    'basic_salary' => (float) $p->basic_salary,
                    'allowances' => (float) $p->allowances,
                    'paye' => (float) $p->paye,
                    'nssf' => (float) $p->nssf,
                    'net_pay' => (float) $p->net_pay,
                    'paid_at' => $p->paid_at?->toIso8601String(),
                ])->values()->all(),
                'total_net' => $items->sum('net_pay'),
                'total_gross' => $items->sum(fn ($p) => (float) $p->basic_salary + (float) $p->allowances),
            ];
        });

        $byYear = $payments->groupBy(fn ($p) => substr($p->period_month, 0, 4))->map(function ($items) {
            return [
                'total_net' => $items->sum('net_pay'),
                'total_gross' => $items->sum(fn ($p) => (float) $p->basic_salary + (float) $p->allowances),
            ];
        })->sortKeysDesc()->all();

        return response()->json([
            'success' => true,
            'data' => [
                'grand_total' => $payments->sum('net_pay'),
                'by_year' => $byYear,
                'by_month' => $byMonth,
            ],
        ]);
    }

    /**
     * Export payroll history as CSV.
     */
    public function export(Request $request): StreamedResponse|Response
    {
        $this->authorize('viewAny', WaiterSalaryPayment::class);

        $restaurantId = Auth::user()->restaurant_id;
        $query = WaiterSalaryPayment::query()
            ->where('restaurant_id', $restaurantId)
            ->with(['user:id,name,global_waiter_number'])
            ->orderByDesc('period_month')
            ->orderBy('user_id');

        $year = $request->string('year')->trim();
        if (preg_match('/^\d{4}$/', $year)) {
            $query->whereRaw('LEFT(period_month, 4) = ?', [$year]);
        }

        $payments = $query->get();
        $filename = 'payroll-history'.($year ? '-'.$year : '').'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($payments): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Period', config('salon.staff'), 'ID', 'Basic', 'Allowances', 'PAYE', 'NSSF', 'Net Pay', 'Paid At']);
            foreach ($payments as $p) {
                fputcsv($out, [
                    $p->period_month,
                    $p->user?->name ?? '',
                    $p->user?->global_waiter_number ?? '',
                    $p->basic_salary,
                    $p->allowances,
                    $p->paye,
                    $p->nssf,
                    $p->net_pay,
                    $p->paid_at?->format('Y-m-d H:i') ?? '',
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
