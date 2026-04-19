<?php

namespace App\Http\Controllers\OrderPortal;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Payment;
use App\Services\SelcomService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductSalesController extends Controller
{
    private function restaurantId(): int
    {
        return (int) session('order_portal_restaurant_id');
    }

    private function waiterId(): int
    {
        return (int) session('order_portal_user_id');
    }

    private function productSaleQuery()
    {
        return Order::withoutGlobalScopes()
            ->where('restaurant_id', $this->restaurantId())
            ->where('waiter_id', $this->waiterId())
            ->where('order_kind', Order::KIND_PRODUCT_SALE);
    }

    /**
     * @return array<string, mixed>
     */
    private function productSaleToArray(Order $order): array
    {
        $order->loadMissing('items');

        $items = $order->items->isNotEmpty()
            ? $order->items->map(fn ($i) => [
                'id' => $i->id,
                'menu_item_id' => $i->menu_item_id,
                'name' => $i->name,
                'quantity' => $i->quantity,
                'price' => $i->price,
                'total' => $i->total,
            ])->values()->all()
            : collect($order->pending_line_items ?? [])->map(fn ($row) => [
                'id' => null,
                'menu_item_id' => (int) ($row['menu_item_id'] ?? 0),
                'name' => (string) ($row['name'] ?? ''),
                'quantity' => (int) ($row['quantity'] ?? 0),
                'price' => (float) ($row['price'] ?? 0),
                'total' => (float) ($row['total'] ?? 0),
            ])->values()->all();

        return [
            'id' => $order->id,
            'table_number' => $order->table_number,
            'customer_phone' => $order->customer_phone,
            'customer_name' => $order->customer_name,
            'total_amount' => $order->total_amount,
            'status' => $order->status,
            'created_at' => $order->created_at->toIso8601String(),
            'items' => $items,
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $tz = config('app.timezone');
        $today = Carbon::today($tz);
        $since = Carbon::today($tz)->subDays(29);

        $base = $this->productSaleQuery()
            ->where('created_at', '>=', $since->startOfDay())
            ->orderByDesc('id');

        $sales = (clone $base)->get();

        $paidToday = $this->productSaleQuery()
            ->where('status', 'paid')
            ->whereDate('created_at', $today)
            ->get();

        $paidPeriod = $this->productSaleQuery()
            ->where('status', 'paid')
            ->where('created_at', '>=', $since->startOfDay())
            ->get();

        return response()->json([
            'data' => [
                'sales' => $sales->map(fn ($o) => $this->productSaleToArray($o))->values()->all(),
                'summary' => [
                    'total_amount_today' => (float) $paidToday->sum('total_amount'),
                    'count_today' => $paidToday->count(),
                    'total_amount_last_30_days' => (float) $paidPeriod->sum('total_amount'),
                    'count_last_30_days' => $paidPeriod->count(),
                ],
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $items = collect($request->input('items', []))
            ->filter(fn ($row) => is_array($row) && ! empty($row['id']))
            ->values()
            ->all();

        $request->merge(['items' => $items]);

        $request->validate([
            'table_number' => 'nullable|string|max:50',
            'customer_phone' => 'nullable|string|max:30',
            'customer_name' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment' => 'required|string|in:cash,push',
            'push_phone' => 'required_if:payment,push|nullable|string|max:30',
        ]);

        $restaurantId = $this->restaurantId();
        $waiterId = $this->waiterId();
        $tableNumber = $request->filled('table_number')
            ? $request->input('table_number')
            : 'Retail';

        $pendingLines = [];
        $totalAmount = 0.0;

        foreach ($request->items as $itemData) {
            $menuItem = MenuItem::withoutGlobalScopes()
                ->where('restaurant_id', $restaurantId)
                ->whereHas('category', fn ($q) => $q->where('catalog_kind', Category::CATALOG_KIND_PRODUCT))
                ->whereKey($itemData['id'])
                ->first();

            if (! $menuItem) {
                throw ValidationException::withMessages([
                    'items' => 'Each item must be a retail product from your menu.',
                ]);
            }

            $qty = (int) $itemData['quantity'];
            $subtotal = $menuItem->price * $qty;
            $totalAmount += $subtotal;
            $pendingLines[] = [
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'quantity' => $qty,
                'price' => (float) $menuItem->price,
                'total' => (float) $subtotal,
            ];
        }

        if ($request->input('payment') === 'cash') {
            $order = DB::transaction(function () use ($restaurantId, $waiterId, $request, $tableNumber, $totalAmount, $pendingLines) {
                $order = Order::withoutGlobalScopes()->create([
                    'restaurant_id' => $restaurantId,
                    'order_kind' => Order::KIND_PRODUCT_SALE,
                    'waiter_id' => $waiterId,
                    'table_number' => $tableNumber,
                    'customer_phone' => $request->input('customer_phone') ?? '',
                    'customer_name' => $request->input('customer_name') ?? '',
                    'scheduled_at' => null,
                    'total_amount' => $totalAmount,
                    'status' => 'paid',
                    'pending_line_items' => null,
                ]);

                foreach ($pendingLines as $line) {
                    $order->items()->create([
                        'menu_item_id' => $line['menu_item_id'],
                        'name' => $line['name'],
                        'quantity' => $line['quantity'],
                        'price' => $line['price'],
                        'total' => $line['total'],
                    ]);
                }

                Payment::create([
                    'order_id' => $order->id,
                    'restaurant_id' => $restaurantId,
                    'waiter_id' => $waiterId,
                    'customer_phone' => $request->input('customer_phone') ?? '',
                    'amount' => $totalAmount,
                    'method' => 'cash',
                    'status' => 'paid',
                    'transaction_reference' => 'PROD-CASH-'.$order->id.'-'.time(),
                ]);

                return $order;
            });

            $order->load('items');

            return response()->json([
                'message' => 'Product sale recorded (cash).',
                'data' => $this->productSaleToArray($order),
            ], Response::HTTP_CREATED);
        }

        /** @var Order $order */
        $order = DB::transaction(function () use ($restaurantId, $waiterId, $request, $tableNumber, $totalAmount, $pendingLines) {
            return Order::withoutGlobalScopes()->create([
                'restaurant_id' => $restaurantId,
                'order_kind' => Order::KIND_PRODUCT_SALE,
                'waiter_id' => $waiterId,
                'table_number' => $tableNumber,
                'customer_phone' => $request->input('customer_phone') ?? '',
                'customer_name' => $request->input('customer_name') ?? '',
                'scheduled_at' => null,
                'total_amount' => $totalAmount,
                'status' => Order::STATUS_PAYMENT_PENDING,
                'pending_line_items' => $pendingLines,
            ]);
        });

        $order->refresh();

        return response()->json([
            'message' => 'Sale created. Initiate mobile payment to complete.',
            'data' => $this->productSaleToArray($order),
            'next' => [
                'action' => 'initiate_push',
                'order_id' => $order->id,
                'phone' => $request->input('push_phone'),
            ],
        ], Response::HTTP_CREATED);
    }

    public function initiatePush(Request $request, int $order, SelcomService $selcom): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'name' => 'nullable|string',
        ]);

        $orderModel = $this->productSaleQuery()
            ->whereKey($order)
            ->firstOrFail();

        if ($orderModel->status !== Order::STATUS_PAYMENT_PENDING) {
            return response()->json([
                'status' => 'error',
                'message' => 'This sale is not waiting for payment.',
            ], 400);
        }

        $restaurant = $orderModel->restaurant()->withoutGlobalScopes()->firstOrFail();

        if (! $restaurant->hasSelcomConfigured()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Selcom haijawekwa. Wasiliana na manager.',
            ], 400);
        }

        $transactionId = 'PROD-'.$orderModel->id.'-'.time();

        $result = $selcom->initiatePayment($restaurant->getSelcomCredentials(), [
            'order_id' => $transactionId,
            'email' => 'customer@taptap.co.tz',
            'name' => $request->name ?? 'Client',
            'phone' => $request->phone,
            'amount' => $orderModel->total_amount,
            'description' => 'Product sale #'.$orderModel->id,
        ]);

        if (isset($result['status']) && $result['status'] === 'success') {
            Payment::create([
                'order_id' => $orderModel->id,
                'restaurant_id' => $restaurant->id,
                'waiter_id' => $this->waiterId(),
                'customer_phone' => $request->phone,
                'amount' => $orderModel->total_amount,
                'method' => 'ussd',
                'status' => 'pending',
                'transaction_reference' => $transactionId,
            ]);
            $orderModel->update(['payment_reference' => $transactionId]);

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
        $orderModel = $this->productSaleQuery()
            ->whereKey($order)
            ->firstOrFail();

        $payment = $orderModel->payments()
            ->where('method', 'ussd')
            ->orderByDesc('created_at')
            ->first();

        if (! $payment || ! $payment->transaction_reference) {
            return response()->json(['status' => 'error', 'message' => 'No active payment found'], 400);
        }

        if ($payment->status === 'paid') {
            return response()->json(['status' => 'paid', 'message' => 'Payment already completed!']);
        }

        $restaurant = $orderModel->restaurant()->withoutGlobalScopes()->firstOrFail();
        $result = $selcom->checkOrderStatus($restaurant->getSelcomCredentials(), $payment->transaction_reference);
        $paymentStatus = $selcom->parsePaymentStatus($result);

        if ($paymentStatus === 'paid') {
            DB::transaction(function () use ($orderModel, $payment) {
                $payment->update(['status' => 'paid']);
                if ($orderModel->pending_line_items) {
                    foreach ($orderModel->pending_line_items as $row) {
                        $orderModel->items()->create([
                            'menu_item_id' => (int) $row['menu_item_id'],
                            'name' => (string) $row['name'],
                            'quantity' => (int) $row['quantity'],
                            'price' => (float) $row['price'],
                            'total' => (float) $row['total'],
                        ]);
                    }
                    $orderModel->update([
                        'pending_line_items' => null,
                        'status' => 'paid',
                    ]);
                } else {
                    $orderModel->update(['status' => 'paid']);
                }
            });
            $orderModel->refresh()->load('items');

            return response()->json(['status' => 'paid', 'message' => 'Payment completed successfully!']);
        }
        if ($paymentStatus === 'failed') {
            $payment->update(['status' => 'failed']);

            return response()->json(['status' => 'failed', 'message' => 'Payment failed or cancelled']);
        }

        return response()->json(['status' => 'pending', 'message' => 'Waiting for payment...']);
    }

    public function destroy(int $order): JsonResponse
    {
        $orderModel = $this->productSaleQuery()->whereKey($order)->firstOrFail();

        if ($orderModel->status !== Order::STATUS_PAYMENT_PENDING) {
            return response()->json([
                'message' => 'Only unpaid push sales can be cancelled.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $orderModel->payments()->delete();
        $orderModel->delete();

        return response()->json(['message' => 'Unpaid product sale cancelled.']);
    }
}
