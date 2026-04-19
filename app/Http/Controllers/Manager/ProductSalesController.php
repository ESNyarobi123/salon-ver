<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductSalesController extends Controller
{
    public function index(Request $request): View
    {
        $restaurantId = (int) auth()->user()->restaurant_id;
        $tz = config('app.timezone');
        $now = Carbon::now($tz);

        $period = $request->input('period', '30');
        if (! in_array($period, ['today', '7', '30', '90', 'custom'], true)) {
            $period = '30';
        }

        [$rangeStart, $rangeEnd] = $this->resolvePeriod($request, $period, $now);

        $status = $request->input('status', 'all');
        if (! in_array($status, ['all', 'paid', 'payment_pending'], true)) {
            $status = 'all';
        }

        $waiterFilter = $request->input('waiter', 'all');

        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();
        $todayStats = $this->statsForPaidRange($restaurantId, $todayStart, $todayEnd, 'all');

        $periodStats = $this->statsForPaidRange($restaurantId, $rangeStart, $rangeEnd, $waiterFilter);

        $pendingInPeriod = Order::query()
            ->where('restaurant_id', $restaurantId)
            ->where('order_kind', Order::KIND_PRODUCT_SALE)
            ->where('status', Order::STATUS_PAYMENT_PENDING)
            ->when($waiterFilter !== 'all' && $waiterFilter !== '', fn ($q) => $q->where('waiter_id', (int) $waiterFilter))
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->count();

        $sales = Order::query()
            ->where('restaurant_id', $restaurantId)
            ->where('order_kind', Order::KIND_PRODUCT_SALE)
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($waiterFilter !== 'all' && $waiterFilter !== '', fn ($q) => $q->where('waiter_id', (int) $waiterFilter))
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->with(['waiter', 'items'])
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $topProducts = OrderItem::query()
            ->whereHas('order', function ($q) use ($restaurantId, $rangeStart, $rangeEnd, $waiterFilter) {
                $q->where('restaurant_id', $restaurantId)
                    ->where('order_kind', Order::KIND_PRODUCT_SALE)
                    ->where('status', 'paid')
                    ->whereBetween('created_at', [$rangeStart, $rangeEnd]);
                if ($waiterFilter !== 'all' && $waiterFilter !== '') {
                    $q->where('waiter_id', (int) $waiterFilter);
                }
            })
            ->selectRaw('name, SUM(quantity) as units_sold, SUM(total) as revenue')
            ->groupBy('name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $waiters = User::query()
            ->where('restaurant_id', $restaurantId)
            ->role('waiter')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('manager.product-sales.index', [
            'period' => $period,
            'status' => $status,
            'waiterFilter' => $waiterFilter,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'todayStats' => $todayStats,
            'periodStats' => $periodStats,
            'pendingInPeriod' => $pendingInPeriod,
            'sales' => $sales,
            'topProducts' => $topProducts,
            'waiters' => $waiters,
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
            $from = Carbon::parse($request->input('date_from', $now->copy()->subDays(29)->toDateString()), $tz)->startOfDay();
            $to = Carbon::parse($request->input('date_to', $now->toDateString()), $tz)->endOfDay();
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

    /**
     * @return array{count: int, revenue: float, units: int}
     */
    private function statsForPaidRange(int $restaurantId, Carbon $from, Carbon $to, mixed $waiterFilter): array
    {
        $orders = Order::query()
            ->where('restaurant_id', $restaurantId)
            ->where('order_kind', Order::KIND_PRODUCT_SALE)
            ->where('status', 'paid')
            ->whereBetween('created_at', [$from, $to])
            ->when($waiterFilter !== 'all' && $waiterFilter !== null && $waiterFilter !== '', fn ($q) => $q->where('waiter_id', (int) $waiterFilter));

        $count = (clone $orders)->count();
        $revenue = (float) (clone $orders)->sum('total_amount');
        $orderIds = (clone $orders)->pluck('id');
        $units = $orderIds->isEmpty()
            ? 0
            : (int) OrderItem::query()->whereIn('order_id', $orderIds)->sum('quantity');

        return [
            'count' => $count,
            'revenue' => $revenue,
            'units' => $units,
        ];
    }
}
