<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StorePayrollPaymentRequest;
use App\Models\User;
use App\Models\WaiterSalaryPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', WaiterSalaryPayment::class);

        $restaurantId = Auth::user()->restaurant_id;
        $restaurant = Auth::user()->restaurant;
        $waiters = User::role('waiter')
            ->activeAtRestaurant($restaurantId)
            ->with(['waiterSalaryPayments' => fn ($q) => $q->where('restaurant_id', $restaurantId)])
            ->orderBy('name')
            ->get();

        $requested = $request->string('month')->trim();
        $currentMonth = preg_match('/^\d{4}-\d{2}$/', $requested) ? $requested : now()->format('Y-m');
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $d = now()->subMonths($i);
            $months[] = ['value' => $d->format('Y-m'), 'label' => $d->format('M Y')];
        }

        return view('manager.payroll.index', [
            'waiters' => $waiters,
            'restaurant' => $restaurant,
            'currentMonth' => $currentMonth,
            'months' => $months,
        ]);
    }

    public function store(StorePayrollPaymentRequest $request): RedirectResponse
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

        return redirect()->route('manager.payroll.index', ['month' => $request->period_month])->with('success', 'Payment confirmed for '.$waiter->name.'.');
    }

    public function history(): View
    {
        $this->authorize('viewAny', WaiterSalaryPayment::class);

        $restaurantId = Auth::user()->restaurant_id;
        $payments = WaiterSalaryPayment::query()
            ->where('restaurant_id', $restaurantId)
            ->with(['user:id,name,global_waiter_number', 'confirmedByUser:id,name'])
            ->orderByDesc('period_month')
            ->orderBy('user_id')
            ->get();

        $byMonth = $payments->groupBy('period_month')->map(function ($items) {
            return [
                'payments' => $items,
                'total_net' => $items->sum('net_pay'),
                'total_gross' => $items->sum(fn ($p) => (float) $p->basic_salary + (float) $p->allowances),
            ];
        });

        $grandTotal = $payments->sum('net_pay');

        $byYear = $payments->groupBy(fn ($p) => substr($p->period_month, 0, 4))->map(function ($items) {
            return [
                'total_net' => $items->sum('net_pay'),
                'total_gross' => $items->sum(fn ($p) => (float) $p->basic_salary + (float) $p->allowances),
            ];
        })->sortKeysDesc();

        return view('manager.payroll.history', [
            'byMonth' => $byMonth,
            'byYear' => $byYear,
            'grandTotal' => $grandTotal,
        ]);
    }

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
