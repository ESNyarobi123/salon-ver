<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'table_number' => 'nullable|string',
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        try {
            $order = DB::transaction(function () use ($validated) {
                $order = Order::create([
                    'restaurant_id' => $validated['restaurant_id'],
                    'table_number' => $validated['table_number'],
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'pending',
                    'total_amount' => 0,
                ]);

                $totalAmount = 0;

                foreach ($validated['items'] as $item) {
                    $menuItem = MenuItem::find($item['menu_item_id']);
                    $price = $menuItem->price;
                    $total = $price * $item['quantity'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $menuItem->id,
                        'name' => $menuItem->name,
                        'quantity' => $item['quantity'],
                        'price' => $price,
                        'total' => $total,
                    ]);

                    $totalAmount += $total;
                }

                $order->update(['total_amount' => $totalAmount]);

                return $order;
            });
        } catch (ValidationException $e) {
            return response()->json([
                'message' => collect($e->errors())->flatten()->first(),
                'errors' => $e->errors(),
            ], 422);
        }

        return response()->json($order->load('orderItems.menuItem'), 201);
    }

    public function show(Order $order)
    {
        return response()->json($order->load('orderItems.menuItem', 'payments'));
    }

    public function status(Order $order)
    {
        return response()->json(['status' => $order->status]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,paid',
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json($order);
    }
}
