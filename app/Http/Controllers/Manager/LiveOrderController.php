<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LiveOrderController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $restaurantId = auth()->user()->restaurant_id;

        $pendingOrders = Order::with(['items.menuItem', 'waiter'])
            ->where('restaurant_id', $restaurantId)
            ->where('order_kind', Order::KIND_BOOKING)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $preparingOrders = Order::with(['items.menuItem', 'waiter'])
            ->where('restaurant_id', $restaurantId)
            ->where('order_kind', Order::KIND_BOOKING)
            ->whereIn('status', ['preparing', 'ready'])
            ->latest()
            ->get();

        $servedOrders = Order::with(['items.menuItem', 'waiter'])
            ->where('restaurant_id', $restaurantId)
            ->where('order_kind', Order::KIND_BOOKING)
            ->where('status', 'served')
            ->latest()
            ->get();

        $paidOrders = Order::with(['items.menuItem', 'waiter'])
            ->where('restaurant_id', $restaurantId)
            ->where('order_kind', Order::KIND_BOOKING)
            ->where('status', 'paid')
            ->whereDate('created_at', $today)
            ->latest()
            ->take(20)
            ->get();

        $tables = \App\Models\Table::where('restaurant_id', $restaurantId)->get();
        $bookingCategories = Category::query()
            ->where('restaurant_id', $restaurantId)
            ->where('catalog_kind', Category::CATALOG_KIND_SERVICE)
            ->with(['menuItems' => fn ($q) => $q->where('is_available', true)->orderBy('name')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->filter(fn (Category $c) => $c->menuItems->isNotEmpty());
        $waiters = User::query()
            ->where('restaurant_id', $restaurantId)
            ->role('waiter')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('manager.orders.live', compact('pendingOrders', 'preparingOrders', 'servedOrders', 'paidOrders', 'tables', 'bookingCategories', 'waiters'));
    }

    /**
     * @return int|null Valid waiter user id for this restaurant, or null for unassigned.
     */
    private function resolveWaiterId(mixed $value, int $restaurantId): ?int
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }
        $id = (int) $value;
        if ($id < 1) {
            return null;
        }
        $user = User::query()
            ->where('restaurant_id', $restaurantId)
            ->whereKey($id)
            ->first();
        if (! $user || ! $user->hasRole('waiter')) {
            throw ValidationException::withMessages([
                'waiter_id' => 'Pick a valid '.strtolower(config('salon.staff')).' from this salon.',
            ]);
        }

        return $user->id;
    }

    public function store(Request $request)
    {
        if ($request->input('waiter_id') === '' || $request->input('waiter_id') === null) {
            $request->merge(['waiter_id' => null]);
        }

        $items = collect($request->input('items', []))
            ->filter(fn ($row) => is_array($row) && ! empty($row['id']))
            ->values()
            ->all();

        if (count($items) < 1) {
            throw ValidationException::withMessages([
                'items' => 'Select at least one service (tick the row and set quantity).',
            ]);
        }

        $request->merge(['items' => $items]);

        $request->validate([
            'table_number' => 'required',
            'waiter_id' => 'nullable|integer',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required|date_format:H:i',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $restaurantId = (int) auth()->user()->restaurant_id;
        $waiterId = $this->resolveWaiterId($request->input('waiter_id'), $restaurantId);
        $scheduledAt = Carbon::parse(
            $request->input('scheduled_date').' '.$request->input('scheduled_time'),
            config('app.timezone')
        );

        $totalAmount = 0;
        $orderItems = [];

        foreach ($request->items as $itemData) {
            $menuItem = \App\Models\MenuItem::query()
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->whereHas('category', fn ($q) => $q->where('catalog_kind', Category::CATALOG_KIND_SERVICE))
                ->whereKey($itemData['id'])
                ->firstOrFail();
            $subtotal = $menuItem->price * $itemData['quantity'];
            $totalAmount += $subtotal;

            $orderItems[] = [
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'quantity' => $itemData['quantity'],
                'price' => $menuItem->price,
                'total' => $subtotal,
            ];
        }

        try {
            DB::transaction(function () use ($request, $orderItems, $totalAmount, $waiterId, $scheduledAt) {
                $order = Order::create([
                    'restaurant_id' => auth()->user()->restaurant_id,
                    'order_kind' => Order::KIND_BOOKING,
                    'waiter_id' => $waiterId,
                    'table_number' => $request->table_number,
                    'customer_phone' => $request->customer_phone,
                    'customer_name' => $request->customer_name,
                    'scheduled_at' => $scheduledAt,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                ]);

                foreach ($orderItems as $item) {
                    $order->items()->create($item);
                }
            });
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->back()->with('success', 'Booking created successfully');
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        if ($order->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        if ($request->has('status')) {
            $request->validate(['status' => 'in:pending,preparing,ready,served,paid']);
            $order->update(['status' => $request->status]);
        }

        if ($request->has('table_number')) {
            if ($request->input('waiter_id') === '' || $request->input('waiter_id') === null) {
                $request->merge(['waiter_id' => null]);
            }
            $request->validate([
                'table_number' => 'required|string|max:50',
                'customer_phone' => 'nullable|string|max:50',
                'customer_name' => 'nullable|string|max:255',
                'waiter_id' => 'nullable|integer',
                'scheduled_date' => 'nullable|date',
                'scheduled_time' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            ]);
            $restaurantId = (int) auth()->user()->restaurant_id;
            $waiterId = $request->exists('waiter_id')
                ? $this->resolveWaiterId($request->input('waiter_id'), $restaurantId)
                : $order->waiter_id;

            if ($request->filled('scheduled_date') && $request->filled('scheduled_time')) {
                $scheduledAt = Carbon::parse(
                    $request->input('scheduled_date').' '.$request->input('scheduled_time'),
                    config('app.timezone')
                );
            } elseif (! $request->filled('scheduled_date') && ! $request->filled('scheduled_time')) {
                $scheduledAt = null;
            } else {
                throw ValidationException::withMessages([
                    'scheduled_date' => 'Enter both appointment date and time, or clear both fields.',
                ]);
            }

            $order->update([
                'table_number' => $request->table_number,
                'customer_phone' => $request->customer_phone,
                'customer_name' => $request->customer_name,
                'waiter_id' => $waiterId,
                'scheduled_at' => $scheduledAt,
            ]);
        }

        return redirect()->back()->with('success', 'Booking updated successfully');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        if ($order->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }
        $order->delete();

        return redirect()->back()->with('success', 'Booking deleted successfully');
    }
}
