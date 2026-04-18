<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    public function index(Request $request)
    {
        $restaurantId = Auth::user()->restaurant_id;

        // Get filter parameters
        $status = $request->get('status', 'all');
        $waiter = $request->get('waiter', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');

        // Build query
        $query = Order::with(['items.menuItem', 'waiter', 'payments'])
            ->where('restaurant_id', $restaurantId);

        // Apply filters
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($waiter !== 'all') {
            $query->where('waiter_id', $waiter);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('table_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Get orders with pagination
        $orders = $query->latest()->paginate(20);

        // Get statistics
        $totalOrders = Order::where('restaurant_id', $restaurantId)->count();
        $completedOrders = Order::where('restaurant_id', $restaurantId)->where('status', 'paid')->count();
        $totalRevenue = Order::where('restaurant_id', $restaurantId)->where('status', 'paid')->sum('total_amount');
        $avgOrderValue = $completedOrders > 0 ? $totalRevenue / $completedOrders : 0;

        // Get waiters for filter dropdown
        $waiters = User::role('waiter')
            ->where('restaurant_id', $restaurantId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('manager.orders.history', compact(
            'orders',
            'totalOrders',
            'completedOrders',
            'totalRevenue',
            'avgOrderValue',
            'waiters',
            'status',
            'waiter',
            'dateFrom',
            'dateTo',
            'search'
        ));
    }

    public function show($id)
    {
        $restaurantId = Auth::user()->restaurant_id;

        $order = Order::with(['items.menuItem', 'waiter', 'payments', 'feedback', 'tip'])
            ->where('restaurant_id', $restaurantId)
            ->findOrFail($id);

        return view('manager.orders.show', compact('order'));
    }

    public function export(Request $request)
    {
        $restaurantId = Auth::user()->restaurant_id;

        $status = $request->get('status', 'all');
        $waiter = $request->get('waiter', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = Order::with(['items.menuItem', 'waiter', 'payments'])
            ->where('restaurant_id', $restaurantId);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($waiter !== 'all') {
            $query->where('waiter_id', $waiter);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->latest()->get();

        $filename = 'order_history_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                config('salon.booking').' ID',
                'Date',
                'Time',
                config('salon.seat'),
                config('salon.customer').' name',
                config('salon.customer').' phone',
                config('salon.staff'),
                'Items',
                'Total Amount',
                'Payment Method',
                'Status',
                'Created At',
            ]);

            // Data
            foreach ($orders as $order) {
                $items = $order->items->map(function ($item) {
                    $name = $item->menuItem ? $item->menuItem->name : $item->name;

                    return "{$item->quantity}x {$name}";
                })->implode(', ');

                fputcsv($file, [
                    $order->id,
                    $order->created_at->format('Y-m-d'),
                    $order->created_at->format('H:i:s'),
                    $order->table_number,
                    $order->customer_name ?? 'N/A',
                    $order->customer_phone ?? 'N/A',
                    $order->waiter ? $order->waiter->name : 'Unassigned',
                    $items,
                    $order->total_amount,
                    $order->payments->isNotEmpty() ? $order->payments->first()->method : 'N/A',
                    $order->status,
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
