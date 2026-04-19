<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_restaurants' => \App\Models\Restaurant::count(),
            'total_waiters' => \App\Models\User::role('waiter')->count(),
            'active_orders' => \App\Models\Order::where('order_kind', \App\Models\Order::KIND_BOOKING)
                ->whereIn('status', ['pending', 'preparing', 'ready'])
                ->count(),
            'total_revenue' => \App\Models\Payment::where('status', 'completed')->sum('amount'),
            'pending_withdrawals' => \App\Models\Withdrawal::where('status', 'pending')->count(),
        ];

        $recent_restaurants = \App\Models\Restaurant::latest()->take(5)->get();
        $recent_activities = \App\Models\Activity::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recent_restaurants', 'recent_activities'));
    }

    public function getStats()
    {
        $stats = [
            'total_restaurants' => \App\Models\Restaurant::count(),
            'total_waiters' => \App\Models\User::role('waiter')->count(),
            'active_orders' => \App\Models\Order::where('order_kind', \App\Models\Order::KIND_BOOKING)
                ->whereIn('status', ['pending', 'preparing', 'ready'])
                ->count(),
            'total_revenue' => \App\Models\Payment::where('status', 'completed')->sum('amount'),
            'pending_withdrawals' => \App\Models\Withdrawal::where('status', 'pending')->count(),
        ];

        return response()->json($stats);
    }
}
