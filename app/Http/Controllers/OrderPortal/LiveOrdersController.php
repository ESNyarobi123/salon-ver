<?php

namespace App\Http\Controllers\OrderPortal;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\Table;
use App\Services\SelcomService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LiveOrdersController extends Controller
{
    private function restaurantId(): int
    {
        return (int) session('order_portal_restaurant_id');
    }

    private function waiterId(): int
    {
        return (int) session('order_portal_user_id');
    }

    private function restaurant(): Restaurant
    {
        return Restaurant::findOrFail($this->restaurantId());
    }

    /**
     * Orders for this restaurant that belong to this waiter only (waiter_id = logged-in waiter).
     */
    private function orderQuery()
    {
        return Order::withoutGlobalScopes()
            ->where('restaurant_id', $this->restaurantId())
            ->where('waiter_id', $this->waiterId());
    }

    public function index(): View|JsonResponse
    {
        $today = Carbon::today();
        $restaurantId = $this->restaurantId();

        $pendingOrders = $this->orderQuery()->with('items.menuItem')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $preparingOrders = $this->orderQuery()->with('items.menuItem')
            ->whereIn('status', ['preparing', 'ready'])
            ->latest()
            ->get();

        $servedOrders = $this->orderQuery()->with('items.menuItem')
            ->where('status', 'served')
            ->latest()
            ->get();

        $paidOrders = $this->orderQuery()->with('items.menuItem')
            ->where('status', 'paid')
            ->whereDate('created_at', $today)
            ->latest()
            ->take(20)
            ->get();

        $tables = Table::withoutGlobalScopes()->where('restaurant_id', $restaurantId)->get();
        $menuItems = MenuItem::withoutGlobalScopes()
            ->where('restaurant_id', $restaurantId)
            ->where('is_available', true)
            ->get();

        $restaurant = $this->restaurant();

        if (request()->expectsJson()) {
            return response()->json([
                'data' => [
                    'pending' => $pendingOrders->map(fn ($o) => $this->orderToArray($o)),
                    'preparing' => $preparingOrders->map(fn ($o) => $this->orderToArray($o)),
                    'served' => $servedOrders->map(fn ($o) => $this->orderToArray($o)),
                    'paid' => $paidOrders->map(fn ($o) => $this->orderToArray($o)),
                ],
                'meta' => [
                    'tables' => $tables->map(fn ($t) => ['id' => $t->id, 'name' => $t->name ?? (string) $t->id]),
                    'menu_items' => $menuItems->map(fn ($m) => [
                        'id' => $m->id,
                        'name' => $m->name,
                        'price' => $m->price,
                        'image_url' => $m->image ? $m->imageUrl() : null,
                    ]),
                    'restaurant' => [
                        'id' => $restaurant->id,
                        'name' => $restaurant->name,
                    ],
                ],
            ]);
        }

        return view('order-portal.orders', compact(
            'pendingOrders', 'preparingOrders', 'servedOrders', 'paidOrders',
            'tables', 'menuItems', 'restaurant'
        ));
    }

    /**
     * @return array<string, mixed>
     */
    private function orderToArray(Order $order): array
    {
        $order->loadMissing('items.menuItem');

        return [
            'id' => $order->id,
            'table_number' => $order->table_number,
            'customer_phone' => $order->customer_phone,
            'customer_name' => $order->customer_name,
            'total_amount' => $order->total_amount,
            'status' => $order->status,
            'created_at' => $order->created_at->toIso8601String(),
            'items' => $order->items->map(fn ($i) => [
                'id' => $i->id,
                'menu_item_id' => $i->menu_item_id,
                'name' => $i->name,
                'quantity' => $i->quantity,
                'price' => $i->price,
                'total' => $i->total,
            ])->values()->all(),
        ];
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'table_number' => 'required|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $restaurantId = $this->restaurantId();
        $totalAmount = 0;
        $orderItems = [];

        foreach ($request->items as $itemData) {
            $menuItem = MenuItem::withoutGlobalScopes()->findOrFail($itemData['id']);
            if ($menuItem->restaurant_id != $restaurantId) {
                abort(403);
            }
            $subtotal = $menuItem->price * (int) $itemData['quantity'];
            $totalAmount += $subtotal;
            $orderItems[] = [
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'quantity' => (int) $itemData['quantity'],
                'price' => $menuItem->price,
                'total' => $subtotal,
            ];
        }

        try {
            $order = DB::transaction(function () use ($restaurantId, $request, $totalAmount, $orderItems) {
                $order = Order::withoutGlobalScopes()->create([
                    'restaurant_id' => $restaurantId,
                    'waiter_id' => $this->waiterId(),
                    'table_number' => $request->table_number,
                    'customer_phone' => $request->customer_phone ?? '',
                    'customer_name' => $request->customer_name ?? '',
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                ]);

                foreach ($orderItems as $item) {
                    $order->items()->create($item);
                }

                return $order;
            });
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => collect($e->errors())->flatten()->first(),
                    'errors' => $e->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        if ($request->expectsJson()) {
            $order->load('items.menuItem');

            return response()->json([
                'message' => 'Booking created successfully.',
                'data' => $this->orderToArray($order),
            ], Response::HTTP_CREATED);
        }

        return redirect()->back()->with('success', 'Booking created successfully.');
    }

    public function update(Request $request, int $order): RedirectResponse|JsonResponse
    {
        $orderModel = $this->orderQuery()->findOrFail($order);

        if ($request->has('status')) {
            $request->validate(['status' => 'in:pending,preparing,ready,served,paid']);
            $orderModel->update(['status' => $request->status]);
        }

        if ($request->has('table_number')) {
            $orderModel->update([
                'table_number' => $request->table_number,
                'customer_phone' => $request->customer_phone ?? '',
                'customer_name' => $request->customer_name ?? '',
            ]);
        }

        if ($request->has('items') && is_array($request->items)) {
            $request->validate([
                'items' => 'array|min:0',
                'items.*.id' => 'required|exists:menu_items,id',
                'items.*.quantity' => 'required|integer|min:0',
            ]);
            $restaurantId = $this->restaurantId();
            try {
                DB::transaction(function () use ($request, $orderModel, $restaurantId) {
                    $totalAmount = 0;
                    $orderModel->items()->delete();
                    foreach ($request->items as $itemData) {
                        $qty = (int) ($itemData['quantity'] ?? 0);
                        if ($qty < 1) {
                            continue;
                        }
                        $menuItem = MenuItem::withoutGlobalScopes()->findOrFail($itemData['id']);
                        if ($menuItem->restaurant_id != $restaurantId) {
                            continue;
                        }
                        $subtotal = $menuItem->price * $qty;
                        $totalAmount += $subtotal;
                        $orderModel->items()->create([
                            'menu_item_id' => $menuItem->id,
                            'name' => $menuItem->name,
                            'quantity' => $qty,
                            'price' => $menuItem->price,
                            'total' => $subtotal,
                        ]);
                    }
                    $orderModel->update(['total_amount' => $totalAmount]);
                });
            } catch (ValidationException $e) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => collect($e->errors())->flatten()->first(),
                        'errors' => $e->errors(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                return redirect()->back()->withErrors($e->errors())->withInput();
            }
        }

        if ($request->expectsJson()) {
            $orderModel->load('items.menuItem');

            return response()->json([
                'message' => 'Booking updated successfully.',
                'data' => $this->orderToArray($orderModel),
            ]);
        }

        return redirect()->back()->with('success', 'Booking updated successfully.');
    }

    public function destroy(int $order): RedirectResponse|JsonResponse
    {
        $orderModel = $this->orderQuery()->findOrFail($order);
        $orderModel->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Booking deleted.',
            ]);
        }

        return redirect()->back()->with('success', 'Booking deleted.');
    }

    public function paymentInitiate(Request $request, SelcomService $selcom): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'phone' => 'required|string',
            'name' => 'nullable|string',
        ]);

        $order = $this->orderQuery()->findOrFail($request->order_id);
        $restaurant = $this->restaurant();

        if (! $restaurant->hasSelcomConfigured()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Selcom haijawekwa. Wasiliana na manager.',
            ], 400);
        }

        $transactionId = 'ORD-'.$order->id.'-'.time();

        $result = $selcom->initiatePayment($restaurant->getSelcomCredentials(), [
            'order_id' => $transactionId,
            'email' => 'customer@taptap.co.tz',
            'name' => $request->name ?? 'Client',
            'phone' => $request->phone,
            'amount' => $order->total_amount,
            'description' => 'Booking #'.$order->id,
        ]);

        if (isset($result['status']) && $result['status'] === 'success') {
            Payment::create([
                'order_id' => $order->id,
                'restaurant_id' => $restaurant->id,
                'customer_phone' => $request->phone,
                'amount' => $order->total_amount,
                'method' => 'ussd',
                'status' => 'pending',
                'transaction_reference' => $transactionId,
            ]);
            $order->update(['payment_reference' => $transactionId]);

            return response()->json([
                'status' => 'success',
                'message' => 'USSD Push sent to '.$request->phone,
                'transaction_id' => $transactionId,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result['message'] ?? 'Failed to initiate payment',
        ], 400);
    }

    public function paymentStatus(int $order, SelcomService $selcom): JsonResponse
    {
        $order = $this->orderQuery()->findOrFail($order);
        $restaurant = $this->restaurant();

        $payment = $order->payments()
            ->where('method', 'ussd')
            ->orderByDesc('created_at')
            ->first();

        if (! $payment || ! $payment->transaction_reference) {
            return response()->json(['status' => 'error', 'message' => 'No active payment found'], 400);
        }

        if ($payment->status === 'paid') {
            return response()->json(['status' => 'paid', 'message' => 'Payment already completed!']);
        }

        $result = $selcom->checkOrderStatus($restaurant->getSelcomCredentials(), $payment->transaction_reference);
        $paymentStatus = $selcom->parsePaymentStatus($result);

        if ($paymentStatus === 'paid') {
            $payment->update(['status' => 'paid']);
            $order->update(['status' => 'paid']);

            return response()->json(['status' => 'paid', 'message' => 'Payment completed successfully!']);
        }
        if ($paymentStatus === 'failed') {
            $payment->update(['status' => 'failed']);

            return response()->json(['status' => 'failed', 'message' => 'Payment failed or cancelled']);
        }

        return response()->json(['status' => 'pending', 'message' => 'Waiting for payment...']);
    }
}
