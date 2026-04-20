<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompletedBookingsController extends Controller
{
    public function index(Request $request): View
    {
        $restaurantId = (int) auth()->user()->restaurant_id;
        $tz = config('app.timezone');
        $now = Carbon::now($tz);

        $period = $request->input('period', 'today');
        if (! in_array($period, ['today', '7', '30', '90', 'custom'], true)) {
            $period = 'today';
        }

        [$rangeStart, $rangeEnd] = $this->resolvePeriod($request, $period, $now);

        $waiterFilter = $request->input('waiter', 'all');
        $search = (string) $request->input('search', '');

        $base = Order::query()
            ->where('restaurant_id', $restaurantId)
            ->where('order_kind', Order::KIND_BOOKING)
            ->where('status', 'paid')
            ->whereBetween('updated_at', [$rangeStart, $rangeEnd])
            ->when($waiterFilter !== 'all' && $waiterFilter !== '', fn ($q) => $q->where('waiter_id', (int) $waiterFilter))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('table_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            });

        $orders = (clone $base)
            ->with(['items.menuItem', 'waiter'])
            ->orderByDesc('updated_at')
            ->paginate(24)
            ->withQueryString();

        $count = (clone $base)->count();
        $revenue = (float) (clone $base)->sum('total_amount');
        $avg = $count > 0 ? $revenue / $count : 0;

        $waiters = User::query()
            ->where('restaurant_id', $restaurantId)
            ->role('waiter')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('manager.orders.completed', [
            'orders' => $orders,
            'period' => $period,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'waiterFilter' => $waiterFilter,
            'search' => $search,
            'waiters' => $waiters,
            'stats' => [
                'count' => $count,
                'revenue' => $revenue,
                'avg' => $avg,
            ],
            'dateFromInput' => $rangeStart->toDateString(),
            'dateToInput' => $rangeEnd->toDateString(),
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolvePeriod(Request $request, string $period, Carbon $now): array
    {
        $tz = config('app.timezone');

        if ($period === 'custom') {
            $from = Carbon::parse($request->input('date_from', $now->copy()->toDateString()), $tz)->startOfDay();
            $to = Carbon::parse($request->input('date_to', $now->copy()->toDateString()), $tz)->endOfDay();
            if ($from->gt($to)) {
                [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
            }

            return [$from, $to];
        }

        if ($period === 'today') {
            return [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
        }

        $days = match ($period) {
            '7' => 7,
            '90' => 90,
            default => 30,
        };

        return [
            $now->copy()->subDays($days - 1)->startOfDay(),
            $now->copy()->endOfDay(),
        ];
    }
}
