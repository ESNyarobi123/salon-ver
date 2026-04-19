<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KitchenController extends Controller
{
    /**
     * Display the kitchen display system
     */
    public function display($token)
    {
        $restaurant = Restaurant::where('kitchen_token', $token)->firstOrFail();

        return view('kitchen.display', compact('restaurant'));
    }

    /**
     * Get orders for kitchen display (API endpoint for real-time updates)
     */
    public function getOrders($token)
    {
        $restaurant = Restaurant::where('kitchen_token', $token)->firstOrFail();

        $orders = Order::with(['items.menuItem', 'waiter'])
            ->where('restaurant_id', $restaurant->id)
            ->where('order_kind', Order::KIND_BOOKING)
            ->whereIn('status', ['pending', 'confirmed', 'preparing'])
            ->orderByRaw("CASE 
                WHEN status = 'pending' THEN 1 
                WHEN status = 'confirmed' THEN 2 
                WHEN status = 'preparing' THEN 3 
                ELSE 4 
            END")
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($order) {
                $createdAt = $order->created_at;
                $now = now();
                $elapsedMinutes = $createdAt->diffInMinutes($now);

                // SLA: 15 min = green, 25 min = yellow, 30+ = red
                $slaStatus = 'green';
                if ($elapsedMinutes > 25) {
                    $slaStatus = 'red';
                } elseif ($elapsedMinutes > 15) {
                    $slaStatus = 'yellow';
                }

                return [
                    'id' => $order->id,
                    'table_number' => $order->table_number,
                    'status' => $order->status,
                    'is_vip' => $order->is_vip ?? false,
                    'waiter_name' => $order->waiter?->name ?? 'Hajateuliwa',
                    'elapsed_minutes' => $elapsedMinutes,
                    'elapsed_time' => $this->formatElapsedTime($elapsedMinutes),
                    'sla_status' => $slaStatus,
                    'created_at' => $createdAt->format('H:i'),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Custom item'),
                            'quantity' => $item->quantity,
                            'notes' => $item->notes ?? '',
                            'status' => $item->status ?? 'pending',
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'orders' => $orders,
            'stats' => [
                'pending' => $orders->where('status', 'pending')->count(),
                'preparing' => $orders->where('status', 'preparing')->count(),
                'total' => $orders->count(),
                'overdue' => $orders->where('sla_status', 'red')->count(),
            ],
            'timestamp' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Update order status from kitchen
     */
    public function updateStatus(Request $request, $token)
    {
        $restaurant = Restaurant::where('kitchen_token', $token)->firstOrFail();

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:preparing,ready,served,completed',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('restaurant_id', $restaurant->id)
            ->with('waiter')
            ->firstOrFail();

        $order->update(['status' => $request->status]);

        // Notify waiter when order is ready
        if ($request->status === 'ready' && $order->waiter) {
            $order->waiter->notify(new \App\Notifications\OrderReadyNotification($order));
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated',
            'order_id' => $order->id,
            'new_status' => $request->status,
        ]);
    }

    /**
     * Mark individual item as cooking/ready
     */
    public function updateItemStatus(Request $request, $token)
    {
        $restaurant = Restaurant::where('kitchen_token', $token)->firstOrFail();

        $request->validate([
            'item_id' => 'required|exists:order_items,id',
            'status' => 'required|in:pending,cooking,ready',
        ]);

        $item = \App\Models\OrderItem::where('id', $request->item_id)
            ->whereHas('order', function ($query) use ($restaurant) {
                $query->where('restaurant_id', $restaurant->id);
            })
            ->firstOrFail();

        $item->update(['status' => $request->status]);

        // If all items are ready, mark order as ready
        $order = $item->order()->with('waiter')->first();
        $allReady = $order->items()->where('status', '!=', 'ready')->count() === 0;
        if ($allReady) {
            $order->update(['status' => 'ready']);

            // Notify waiter when order is ready
            if ($order->waiter) {
                $order->waiter->notify(new \App\Notifications\OrderReadyNotification($order));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Service line status updated',
            'item_id' => $item->id,
            'new_status' => $request->status,
            'order_ready' => $allReady,
        ]);
    }

    /**
     * Generate new kitchen token (Manager only)
     */
    public function generateToken(Request $request)
    {
        $user = Auth::user();
        $restaurant = Restaurant::findOrFail($user->restaurant_id);

        $restaurant->update([
            'kitchen_token' => Str::random(32),
            'kitchen_token_generated_at' => now(),
        ]);

        return back()->with('success', 'Salon floor display link generated successfully!');
    }

    /**
     * Revoke kitchen token (Manager only)
     */
    public function revokeToken(Request $request)
    {
        $user = Auth::user();
        $restaurant = Restaurant::findOrFail($user->restaurant_id);

        $restaurant->update([
            'kitchen_token' => null,
            'kitchen_token_generated_at' => null,
        ]);

        return back()->with('success', 'Salon floor display link revoked!');
    }

    /**
     * Get order history for kitchen display (completed/served orders)
     */
    public function getOrderHistory(Request $request, $token)
    {
        $restaurant = Restaurant::where('kitchen_token', $token)->firstOrFail();

        // 'ready' orders should appear in BOTH active orders (ready sidebar) AND history
        // so chefs can see orders waiting to be served
        $query = Order::with(['items.menuItem', 'waiter'])
            ->where('restaurant_id', $restaurant->id)
            ->whereIn('status', ['ready', 'served', 'completed', 'paid'])
            ->orderBy('updated_at', 'desc');

        // Filter by date - show all by default, or filter if date provided
        if ($request->has('date') && $request->date) {
            $query->whereDate('updated_at', $request->date);
        }
        // If no date provided, show last 7 days by default (not just today)
        else {
            $query->where('updated_at', '>=', now()->subDays(7));
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by table
        if ($request->has('table')) {
            $query->where('table_number', $request->table);
        }

        $orders = $query->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'table_number' => $order->table_number,
                'status' => $order->status,
                'is_vip' => $order->is_vip ?? false,
                'waiter_name' => $order->waiter?->name ?? 'Hajateuliwa',
                'total_amount' => $order->total_amount,
                'completed_at' => $order->updated_at->format('H:i'),
                'completed_time' => $order->updated_at->diffForHumans(),
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Custom item'),
                        'quantity' => $item->quantity,
                        'status' => $item->status ?? 'pending',
                    ];
                }),
            ];
        });

        // Get unique tables for filter - use same date logic
        $tablesQuery = Order::where('restaurant_id', $restaurant->id)
            ->whereIn('status', ['ready', 'served', 'completed', 'paid']);

        if ($request->has('date') && $request->date) {
            $tablesQuery->whereDate('updated_at', $request->date);
        } else {
            $tablesQuery->where('updated_at', '>=', now()->subDays(7));
        }

        $tables = $tablesQuery->distinct()
            ->pluck('table_number')
            ->sort()
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'orders' => $orders,
            'tables' => $tables,
            'stats' => [
                'total' => $orders->count(),
                'ready' => $orders->where('status', 'ready')->count(),
                'served' => $orders->where('status', 'served')->count(),
                'completed' => $orders->whereIn('status', ['completed', 'paid'])->count(),
            ],
            'date' => $request->date ?? today()->toDateString(),
        ]);
    }

    /**
     * Format elapsed time for display
     */
    private function formatElapsedTime($minutes)
    {
        if ($minutes < 1) {
            return 'Just now';
        } elseif ($minutes < 60) {
            return $minutes.'m';
        } else {
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;

            return $hours.'h '.$mins.'m';
        }
    }
}
