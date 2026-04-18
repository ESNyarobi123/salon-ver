<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function performance(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        [$start, $end] = $this->getDateRange($period, $startDate, $endDate);

        $totalOrders = Order::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $totalRevenue = Payment::where('restaurant_id', $restaurantId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $ordersWithRating = Order::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$start, $end])
            ->whereHas('feedback')
            ->with('feedback')
            ->get();

        $avgRating = $ordersWithRating->isNotEmpty()
            ? round($ordersWithRating->avg('feedback.rating'), 2)
            : 0;

        $waiterStats = User::role('waiter')
            ->where('restaurant_id', $restaurantId)
            ->with(['orders' => function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            }, 'tips' => function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            }])
            ->get()
            ->map(function ($waiter) {
                $orders = $waiter->orders;
                $tips = $waiter->tips;

                $ordersWithFeedback = $orders->filter(function ($order) {
                    return $order->feedback !== null;
                });

                $avgRating = $ordersWithFeedback->isNotEmpty()
                    ? round($ordersWithFeedback->avg('feedback.rating'), 2)
                    : 0;

                return [
                    'id' => $waiter->id,
                    'name' => $waiter->name,
                    'orders_count' => $orders->count(),
                    'tips_earned' => $tips->sum('amount'),
                    'avg_rating' => $avgRating,
                ];
            })
            ->sortByDesc('orders_count');

        $topPerformer = $waiterStats->first();

        return view('manager.reports.performance', compact(
            'totalOrders',
            'totalRevenue',
            'avgRating',
            'waiterStats',
            'topPerformer',
            'period',
            'startDate',
            'endDate'
        ));
    }

    public function exportPerformance(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        [$start, $end] = $this->getDateRange($period, $startDate, $endDate);

        $waiterStats = User::role('waiter')
            ->where('restaurant_id', $restaurantId)
            ->with(['orders' => function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            }, 'tips' => function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            }])
            ->get()
            ->map(function ($waiter) {
                $orders = $waiter->orders;
                $tips = $waiter->tips;

                $ordersWithFeedback = $orders->filter(function ($order) {
                    return $order->feedback !== null;
                });

                $avgRating = $ordersWithFeedback->isNotEmpty()
                    ? round($ordersWithFeedback->avg('feedback.rating'), 2)
                    : 0;

                return [
                    'name' => $waiter->name,
                    'orders_count' => $orders->count(),
                    'tips_earned' => $tips->sum('amount'),
                    'avg_rating' => $avgRating,
                ];
            });

        $filename = 'performance_report_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($waiterStats) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                config('salon.staff').' name',
                'Bookings handled',
                'Tips earned (Tsh)',
                'Average rating',
            ]);

            foreach ($waiterStats as $stat) {
                fputcsv($file, [
                    $stat['name'],
                    $stat['orders_count'],
                    number_format($stat['tips_earned'], 2),
                    $stat['avg_rating'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getDateRange($period, $startDate = null, $endDate = null)
    {
        switch ($period) {
            case 'today':
                return [Carbon::today(), Carbon::now()];
            case 'week':
                return [Carbon::now()->startOfWeek(), Carbon::now()];
            case 'month':
                return [Carbon::now()->startOfMonth(), Carbon::now()];
            case 'custom':
                return [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ];
            default:
                return [Carbon::today(), Carbon::now()];
        }
    }
}
