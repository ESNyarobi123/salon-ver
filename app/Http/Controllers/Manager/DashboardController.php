<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $restaurantId = Auth::user()->restaurant_id;
        $today = Carbon::today();

        // Stats
        $totalOrdersToday = Order::where('restaurant_id', $restaurantId)->whereDate('created_at', $today)->count();
        $revenueToday = Order::where('restaurant_id', $restaurantId)->whereDate('created_at', $today)->where('status', 'paid')->sum('total_amount');
        $avgRating = Feedback::whereHas('order', function ($q) use ($restaurantId) {
            $q->where('restaurant_id', $restaurantId);
        })->avg('rating') ?? 0;
        $waitersOnline = User::role('waiter')->where('restaurant_id', $restaurantId)->where('is_online', true)->count();

        // Live Orders
        $pendingOrders = Order::with('items.menuItem')->where('restaurant_id', $restaurantId)->where('status', 'pending')->latest()->get();
        $preparingOrders = Order::with('items.menuItem')->where('restaurant_id', $restaurantId)->where('status', 'preparing')->latest()->get();
        $servedOrders = Order::with('items.menuItem')->where('restaurant_id', $restaurantId)->where('status', 'served')->latest()->get();
        $paidOrders = Order::with('items.menuItem')->where('restaurant_id', $restaurantId)->where('status', 'paid')->whereDate('created_at', $today)->latest()->take(10)->get();

        // Feedback
        $recentFeedback = Feedback::with('order')->whereHas('order', function ($q) use ($restaurantId) {
            $q->where('restaurant_id', $restaurantId);
        })->latest()->take(5)->get();

        // Tips: not shown to manager (policy: don't show tips to manager)
        $waiterTips = collect();

        $restaurant = $restaurantId
            ? Restaurant::query()->find($restaurantId)
            : null;

        $managerQuickLinks = $this->buildManagerQuickLinks($restaurant);

        return view('manager.dashboard', compact(
            'totalOrdersToday',
            'revenueToday',
            'avgRating',
            'waitersOnline',
            'pendingOrders',
            'preparingOrders',
            'servedOrders',
            'paidOrders',
            'recentFeedback',
            'waiterTips',
            'restaurant',
            'managerQuickLinks'
        ));
    }

    /**
     * @return list<array{href: string, title: string, subtitle?: string, external?: bool}>
     */
    protected function buildManagerQuickLinks(?Restaurant $restaurant): array
    {
        $links = [
            ['href' => route('manager.orders.live'), 'title' => config('salon.live_bookings')],
            ['href' => route('manager.orders.history'), 'title' => config('salon.booking_history')],
            ['href' => route('manager.menu.index'), 'title' => config('salon.services')],
            ['href' => route('manager.menu-image.index'), 'title' => config('salon.service_menu_image')],
            ['href' => route('manager.waiters.index'), 'title' => config('salon.staff_plural').config('salon.manager_nav_staff_team_suffix')],
            ['href' => route('manager.waiters.history'), 'title' => config('salon.manager_nav_staff_history')],
            ['href' => route('manager.tables.index'), 'title' => config('salon.seat_management_title')],
            ['href' => route('manager.payroll.index'), 'title' => config('salon.manager_nav_payroll')],
            ['href' => route('manager.payroll.history'), 'title' => config('salon.manager_nav_payroll_history')],
            ['href' => route('manager.payments.index'), 'title' => config('salon.manager_nav_payments')],
            ['href' => route('manager.feedback.index'), 'title' => config('salon.manager_nav_customer_feedback')],
            ['href' => route('manager.reports.performance'), 'title' => config('salon.manager_nav_reports')],
            ['href' => route('manager.api.index'), 'title' => config('salon.manager_nav_api')],
            ['href' => route('manager.help.index'), 'title' => config('salon.manager_nav_help')],
        ];

        if ($restaurant?->kitchen_token) {
            $links[] = [
                'href' => url('/kitchen/display/'.$restaurant->kitchen_token),
                'title' => config('salon.manager_kds_open'),
                'subtitle' => config('salon.floor_display'),
                'external' => true,
            ];
        }

        return $links;
    }

    public function getStats()
    {
        $restaurantId = Auth::user()->restaurant_id;
        $today = Carbon::today();

        $waitersOnline = User::role('waiter')->where('restaurant_id', $restaurantId)->where('is_online', true)->count();

        $stats = [
            'total_orders_today' => Order::where('restaurant_id', $restaurantId)->whereDate('created_at', $today)->count(),
            'revenue_today' => Order::where('restaurant_id', $restaurantId)->whereDate('created_at', $today)->where('status', 'paid')->sum('total_amount'),
            'avg_rating' => number_format(Feedback::whereHas('order', function ($q) use ($restaurantId) {
                $q->where('restaurant_id', $restaurantId);
            })->avg('rating') ?? 0, 1),
            'waiters_online' => $waitersOnline,
            'waiters_online_label' => $waitersOnline.' '.config('salon.manager_staff_online_suffix'),
        ];

        return response()->json($stats);
    }
}
