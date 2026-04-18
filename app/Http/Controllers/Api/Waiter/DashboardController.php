<?php

namespace App\Http\Controllers\Api\Waiter;

use App\Http\Controllers\Controller;
use App\Models\CustomerRequest;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Table;
use App\Models\Tip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Waiter Dashboard API - Returns stats and data for mobile app
     */
    public function index(): JsonResponse
    {
        $waiter = Auth::user();
        $today = Carbon::today();
        $isLinked = $waiter->restaurant_id !== null;

        $tipsToday = Tip::where('waiter_id', $waiter->id)->whereDate('created_at', $today)->sum('amount');
        $tipsThisWeek = Tip::where('waiter_id', $waiter->id)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');

        $myActiveOrders = Order::where('waiter_id', $waiter->id)->whereIn('status', ['pending', 'preparing', 'ready'])->count();
        $restaurantActiveOrders = $isLinked ? Order::where('restaurant_id', $waiter->restaurant_id)->whereIn('status', ['pending', 'preparing', 'ready'])->count() : 0;
        $readyToServeOrders = $isLinked ? Order::where('restaurant_id', $waiter->restaurant_id)->where('status', 'ready')->count() : 0;

        $unassignedOrders = $isLinked && $waiter->is_online
            ? Order::with('items.menuItem')
                ->where('restaurant_id', $waiter->restaurant_id)
                ->whereNull('waiter_id')
                ->whereIn('status', ['pending', 'preparing', 'ready'])
                ->latest()
                ->get()
                ->map(fn ($order) => [
                    'id' => $order->id,
                    'table_number' => $order->table_number,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at->toIso8601String(),
                    'items' => $order->items->map(fn ($item) => [
                        'name' => $item->name ?? ($item->menuItem?->name ?? 'Custom item'),
                        'quantity' => $item->quantity,
                    ]),
                ])
            : collect();

        $pendingRequests = $isLinked && $waiter->is_online
            ? CustomerRequest::where('status', 'pending')
                ->where('restaurant_id', $waiter->restaurant_id)
                ->where(fn ($q) => $q->whereNull('waiter_id')->orWhere('waiter_id', $waiter->id))
                ->latest()->get()->map(fn ($req) => [
                    'id' => $req->id,
                    'type' => $req->type,
                    'table_number' => $req->table_number,
                    'created_at' => $req->created_at->toIso8601String(),
                ])
            : collect();

        $feedbackVisibleAt = Carbon::now()->subMinutes(config('services.feedback.visible_after_minutes', 60));
        $recentFeedback = Feedback::where(function ($query) use ($waiter) {
            $query->where('waiter_id', $waiter->id)
                ->orWhereHas('order', fn ($q) => $q->where('waiter_id', $waiter->id));
        })->where('created_at', '<=', $feedbackVisibleAt)->latest()->take(5)->get()->map(fn ($f) => [
            'id' => $f->id,
            'rating' => $f->rating,
            'comment' => $f->comment,
            'created_at' => $f->created_at->toIso8601String(),
        ]);

        $myOrders = Order::with('items.menuItem')
            ->where('waiter_id', $waiter->id)
            ->whereDate('created_at', $today)
            ->latest()
            ->get()
            ->map(fn ($order) => [
                'id' => $order->id,
                'table_number' => $order->table_number,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'items_count' => $order->items->count(),
                'created_at' => $order->created_at->toIso8601String(),
            ]);

        $waiterPayload = [
            'id' => $waiter->id,
            'name' => $waiter->name,
            'global_waiter_number' => $waiter->global_waiter_number,
        ];
        if ($isLinked) {
            $waiterPayload['waiter_code'] = $waiter->waiter_code;
            $waiterPayload['waiter_qr_url'] = $waiter->waiter_qr_url;
        } else {
            $waiterPayload['waiter_code'] = null;
            $waiterPayload['waiter_qr_url'] = null;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'is_linked' => $isLinked,
                'waiter' => $waiterPayload,
                'is_online' => $waiter->is_online,
                'last_online_at' => $waiter->last_online_at?->toIso8601String(),
                'stats' => [
                    'tips_today' => $tipsToday,
                    'tips_today_amount' => $tipsToday,
                    'tips_this_week' => $tipsThisWeek,
                    'tips_this_week_amount' => $tipsThisWeek,
                    'total_tips_received' => Tip::where('waiter_id', $waiter->id)->sum('amount'),
                    'my_active_orders' => $myActiveOrders,
                    'restaurant_active_orders' => $restaurantActiveOrders,
                    'ready_to_serve' => $readyToServeOrders,
                    'pending_requests' => $pendingRequests->count(),
                ],
                'unassigned_orders' => $unassignedOrders->values(),
                'pending_requests' => $pendingRequests->values(),
                'recent_feedback' => $recentFeedback->values(),
                'my_orders_today' => $myOrders->values(),
            ],
        ]);
    }

    /**
     * Quick stats only (for polling/refresh)
     */
    public function stats(): JsonResponse
    {
        $waiter = Auth::user();
        $today = Carbon::today();
        $isLinked = $waiter->restaurant_id !== null;

        $tipsTodayAmount = Tip::where('waiter_id', $waiter->id)->whereDate('created_at', $today)->sum('amount');

        $readyToServe = $isLinked ? Order::where('restaurant_id', $waiter->restaurant_id)->where('status', 'ready')->count() : 0;
        $pendingRequestsCount = $isLinked && $waiter->is_online
            ? CustomerRequest::where('restaurant_id', $waiter->restaurant_id)->where('status', 'pending')
                ->where(fn ($q) => $q->whereNull('waiter_id')->orWhere('waiter_id', $waiter->id))
                ->count()
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'is_linked' => $isLinked,
                'is_online' => $waiter->is_online,
                'tips_today' => $tipsTodayAmount,
                'tips_today_amount' => $tipsTodayAmount,
                'total_tips_received' => Tip::where('waiter_id', $waiter->id)->sum('amount'),
                'my_active_orders' => Order::where('waiter_id', $waiter->id)->whereIn('status', ['pending', 'preparing', 'ready'])->count(),
                'ready_to_serve' => $readyToServe,
                'pending_requests' => $pendingRequestsCount,
            ],
        ]);
    }

    /**
     * Claim an unassigned order
     */
    public function claimOrder(Order $order): JsonResponse
    {
        if ($order->waiter_id) {
            return response()->json([
                'success' => false,
                'message' => 'This booking has already been claimed by another stylist.',
            ], 422);
        }

        $waiterId = Auth::id();
        $order->update(['waiter_id' => $waiterId]);

        Tip::where('order_id', $order->id)
            ->whereNull('waiter_id')
            ->update(['waiter_id' => $waiterId]);

        return response()->json([
            'success' => true,
            'message' => 'Booking #'.$order->id.' is now assigned to you!',
            'data' => [
                'order_id' => $order->id,
                'table_number' => $order->table_number,
            ],
        ]);
    }

    /**
     * Mark customer request (call waiter / request bill) as completed.
     * Waiter can only complete requests assigned to them or unassigned (waiter_id null).
     */
    public function completeRequest(CustomerRequest $customerRequest): JsonResponse
    {
        $waiter = Auth::user();
        if ($customerRequest->waiter_id !== null && (int) $customerRequest->waiter_id !== (int) $waiter->id) {
            return response()->json([
                'success' => false,
                'message' => 'This request is assigned to another stylist.',
            ], 403);
        }

        $customerRequest->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Request marked as attended!',
            'data' => ['request_id' => $customerRequest->id],
        ]);
    }

    /**
     * List waiter's orders (paginated)
     */
    public function orders(): JsonResponse
    {
        $waiter = Auth::user();
        $orders = Order::with('items.menuItem')
            ->where('waiter_id', $waiter->id)
            ->latest()
            ->paginate(15);

        $orders->getCollection()->transform(function ($order) {
            return [
                'id' => $order->id,
                'table_number' => $order->table_number,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at->toIso8601String(),
                'items' => $order->items->map(fn ($item) => [
                    'name' => $item->name ?? ($item->menuItem?->name ?? 'Custom item'),
                    'quantity' => $item->quantity,
                    'price' => $item->price ?? $item->menuItem?->price,
                ]),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * List waiter's tips (paginated)
     */
    public function tips(): JsonResponse
    {
        $waiter = Auth::user();
        $tips = Tip::where('waiter_id', $waiter->id)->latest()->paginate(15);
        $totalTips = Tip::where('waiter_id', $waiter->id)->sum('amount');

        $tips->getCollection()->transform(fn ($t) => [
            'id' => $t->id,
            'order_id' => $t->order_id,
            'amount' => (float) $t->amount,
            'amount_received' => (float) $t->amount,
            'created_at' => $t->created_at->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'total_tips' => $totalTips,
                'total_amount_received' => $totalTips,
                'summary' => [
                    'total_tips' => $totalTips,
                    'today' => Tip::where('waiter_id', $waiter->id)->whereDate('created_at', Carbon::today())->sum('amount'),
                    'this_week' => Tip::where('waiter_id', $waiter->id)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount'),
                ],
                'tips' => $tips,
            ],
        ]);
    }

    /**
     * List waiter's ratings/feedback (paginated).
     * Anonymous: no customer/table identity. Comments appear after configured delay.
     */
    public function ratings(): JsonResponse
    {
        $waiter = Auth::user();
        $feedbackVisibleAt = Carbon::now()->subMinutes(config('services.feedback.visible_after_minutes', 60));

        $feedbacks = Feedback::where(function ($query) use ($waiter) {
            $query->where('waiter_id', $waiter->id)
                ->orWhereHas('order', fn ($q) => $q->where('waiter_id', $waiter->id));
        })->where('created_at', '<=', $feedbackVisibleAt)->latest()->paginate(15);

        $feedbacks->getCollection()->transform(function ($f) {
            return [
                'id' => $f->id,
                'rating' => $f->rating,
                'comment' => $f->comment,
                'created_at' => $f->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $feedbacks,
        ]);
    }

    /**
     * List pending customer requests (call waiter / request bill)
     */
    public function pendingRequests(): JsonResponse
    {
        $waiter = Auth::user();
        if (! $waiter->is_online) {
            return response()->json(['success' => true, 'data' => []]);
        }
        $requests = CustomerRequest::with('waiter')
            ->where('status', 'pending')
            ->where(fn ($q) => $q->whereNull('waiter_id')->orWhere('waiter_id', $waiter->id))
            ->latest()->get()->map(function ($req) {
                $typeLabel = $req->type === 'request_bill' ? 'Request bill' : 'Call stylist';

                return [
                    'id' => $req->id,
                    'type' => $req->type,
                    'type_label' => $typeLabel,
                    'table_number' => $req->table_number ?? '-',
                    'waiter_id' => $req->waiter_id,
                    'waiter_name' => $req->waiter?->name,
                    'created_at' => $req->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $requests->values(),
        ]);
    }

    /**
     * List completed (successful) payments for tables this waiter served.
     * Used for "Payment confirmation → waiter": waiter sees "Payment successful – from Table X".
     */
    public function payments(): JsonResponse
    {
        $waiter = Auth::user();

        $payments = Payment::query()
            ->where('status', 'paid')
            ->where(function ($query) use ($waiter) {
                $query->where('waiter_id', $waiter->id)
                    ->orWhereHas('order', fn ($q) => $q->where('waiter_id', $waiter->id));
            })
            ->with('order:id,table_number,waiter_id,restaurant_id')
            ->latest()
            ->paginate(15);

        $payments->getCollection()->transform(function (Payment $payment) {
            $tableNumber = $payment->order?->table_number ?? null;

            return [
                'id' => $payment->id,
                'order_id' => $payment->order_id,
                'amount' => (float) $payment->amount,
                'method' => $payment->method,
                'status' => $payment->status,
                'table_number' => $tableNumber,
                'message' => $tableNumber
                ? "Payment successful – from seat {$tableNumber}"
                : 'Payment successful',
                'created_at' => $payment->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }

    /**
     * List tables assigned to the current waiter (for hand over before departure).
     */
    public function myTables(): JsonResponse
    {
        $waiter = Auth::user();
        $tables = Table::where('waiter_id', $waiter->id)
            ->orderBy('name')
            ->get(['id', 'name', 'table_tag', 'waiter_id']);

        return response()->json([
            'success' => true,
            'data' => $tables->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'table_tag' => $t->table_tag,
            ]),
        ]);
    }

    /**
     * List other waiters in the same restaurant (for hand over target).
     */
    public function colleagues(): JsonResponse
    {
        $waiter = Auth::user();
        $colleagues = User::where('restaurant_id', $waiter->restaurant_id)
            ->where('id', '!=', $waiter->id)
            ->whereHas('roles', fn ($q) => $q->where('name', 'waiter'))
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $colleagues->map(fn ($u) => ['id' => $u->id, 'name' => $u->name]),
        ]);
    }

    /**
     * Hand over selected tables to another waiter or unassign (before departure).
     */
    public function handoverTables(Request $request): JsonResponse
    {
        $waiter = Auth::user();
        $request->validate([
            'table_ids' => 'required|array',
            'table_ids.*' => 'integer|exists:tables,id',
            'hand_over_to_waiter_id' => 'nullable|integer|exists:users,id',
        ]);

        $tableIds = $request->input('table_ids');
        $targetWaiterId = $request->input('hand_over_to_waiter_id');

        $tables = Table::whereIn('id', $tableIds)->where('waiter_id', $waiter->id)->get();
        if ($tables->count() !== count($tableIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Some seats are not assigned to you.',
            ], 403);
        }

        if ($targetWaiterId !== null) {
            $target = User::where('id', $targetWaiterId)
                ->where('restaurant_id', $waiter->restaurant_id)
                ->whereHas('roles', fn ($q) => $q->where('name', 'waiter'))
                ->first();
            if (! $target) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid hand over target.',
                ], 422);
            }
        }

        Table::whereIn('id', $tableIds)->update(['waiter_id' => $targetWaiterId]);

        return response()->json([
            'success' => true,
            'message' => $targetWaiterId
                ? 'Seats handed over successfully.'
                : 'Seats unassigned successfully.',
        ]);
    }

    /**
     * Toggle waiter online/offline. When going offline, last_online_at is set.
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'is_online' => 'required|boolean',
        ]);

        $waiter = Auth::user();
        $isOnline = (bool) $request->is_online;

        $waiter->is_online = $isOnline;
        $waiter->last_online_at = $isOnline ? null : now();
        $waiter->save();

        return response()->json([
            'success' => true,
            'message' => $isOnline ? 'You are now online. You will receive client calls and new bookings.' : 'You are now offline. You will not receive new client calls or bookings.',
            'data' => [
                'is_online' => $waiter->is_online,
                'last_online_at' => $waiter->last_online_at?->toIso8601String(),
            ],
        ]);
    }
}
