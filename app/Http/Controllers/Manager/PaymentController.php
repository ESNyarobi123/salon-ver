<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\SelcomService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected SelcomService $selcom;

    public function __construct(SelcomService $selcom)
    {
        $this->selcom = $selcom;
    }

    public function index(Request $request)
    {
        $restaurant = Auth::user()->restaurant;

        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        [$start, $end] = $this->getDateRange($period, $startDate, $endDate);

        // Get payments for this restaurant only
        $payments = Payment::where('restaurant_id', $restaurant->id)
            ->with(['order', 'waiter'])
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['paid', 'completed'])
            ->latest()
            ->paginate(20);

        // Get tips for the period
        $tips = \App\Models\Tip::where('restaurant_id', $restaurant->id)
            ->whereBetween('created_at', [$start, $end])
            ->with('waiter')
            ->get();

        // Calculate statistics
        $totalRevenue = Payment::where('restaurant_id', $restaurant->id)
            ->whereIn('status', ['paid', 'completed'])
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $totalTips = $tips->sum('amount');

        $totalOrders = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Revenue by payment method
        $cashRevenue = Payment::where('restaurant_id', $restaurant->id)
            ->where('method', 'cash')
            ->whereIn('status', ['paid', 'completed'])
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $ussdRevenue = Payment::where('restaurant_id', $restaurant->id)
            ->where('method', 'ussd')
            ->whereIn('status', ['paid', 'completed'])
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $mobileRevenue = Payment::where('restaurant_id', $restaurant->id)
            ->where('method', 'mobile')
            ->whereIn('status', ['paid', 'completed'])
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        // Daily revenue for chart - based on selected time period
        $dailyRevenue = $this->getDailyRevenue($restaurant->id, $start, $end, $period);

        return view('manager.payments.index', compact(
            'payments',
            'tips',
            'totalRevenue',
            'totalTips',
            'totalOrders',
            'avgOrderValue',
            'cashRevenue',
            'ussdRevenue',
            'mobileRevenue',
            'dailyRevenue',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get daily revenue data for chart based on time period
     */
    private function getDailyRevenue($restaurantId, $start, $end, $period)
    {
        $dailyRevenue = [];

        // Calculate number of days in range
        $daysDiff = $start->diffInDays($end);

        // For periods longer than 31 days, show weekly data instead
        if ($daysDiff > 31) {
            $weeks = ceil($daysDiff / 7);
            for ($i = $weeks - 1; $i >= 0; $i--) {
                $weekStart = $end->copy()->subWeeks($i + 1)->startOfWeek();
                $weekEnd = $end->copy()->subWeeks($i)->startOfWeek();

                $revenue = Payment::where('restaurant_id', $restaurantId)
                    ->whereIn('status', ['paid', 'completed'])
                    ->whereBetween('created_at', [$weekStart, $weekEnd])
                    ->sum('amount');

                $dailyRevenue[] = [
                    'date' => $weekStart->format('M d'),
                    'revenue' => $revenue,
                    'label' => 'Week of '.$weekStart->format('M d'),
                ];
            }

            return $dailyRevenue;
        }

        // For shorter periods, show daily breakdown
        $current = $start->copy();
        while ($current <= $end) {
            $revenue = Payment::where('restaurant_id', $restaurantId)
                ->whereIn('status', ['paid', 'completed'])
                ->whereDate('created_at', $current)
                ->sum('amount');

            $dailyRevenue[] = [
                'date' => $current->format('M d'),
                'revenue' => $revenue,
                'label' => $current->format('D, M d'),
            ];

            $current->addDay();
        }

        return $dailyRevenue;
    }

    /**
     * Initiate Selcom USSD Push Payment
     */
    public function initiateSelcom(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'name' => 'nullable|string',
        ]);

        $order = Order::findOrFail($request->order_id);
        $restaurant = Auth::user()->restaurant;

        if (! $restaurant->hasSelcomConfigured()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Selcom payment gateway not configured. Go to API Settings to configure.',
            ], 400);
        }

        $transactionId = 'ORD-'.$order->id.'-'.time();

        $result = $this->selcom->initiatePayment($restaurant->getSelcomCredentials(), [
            'order_id' => $transactionId,
            'email' => $request->email ?? 'customer@taptap.co.tz',
            'name' => $request->name ?? 'Customer',
            'phone' => $request->phone,
            'amount' => $order->total_amount,
            'description' => 'Booking #'.$order->id,
        ]);

        if (isset($result['status']) && $result['status'] === 'success') {
            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'restaurant_id' => $restaurant->id,
                'customer_phone' => $request->phone,
                'amount' => $order->total_amount,
                'method' => 'ussd',
                'status' => 'pending',
                'transaction_reference' => $transactionId,
            ]);

            // Save the external order_id in the order
            $order->update(['payment_reference' => $transactionId]);

            return response()->json([
                'status' => 'success',
                'message' => 'USSD Push sent to '.$request->phone,
                'payment_id' => $payment->id,
                'transaction_id' => $transactionId,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result['message'] ?? 'Failed to initiate payment',
        ], 400);
    }

    /**
     * Check Selcom Payment Status (Polling)
     */
    public function checkSelcomStatus($orderId)
    {
        $order = Order::findOrFail($orderId);
        $restaurant = Auth::user()->restaurant;

        // Find the latest pending payment for this order
        $payment = Payment::where('order_id', $order->id)
            ->where('method', 'ussd')
            ->latest()
            ->first();

        if (! $payment || ! $payment->transaction_reference) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active payment transaction found',
            ], 400);
        }

        // If already paid, return immediately
        if ($payment->status === 'paid') {
            return response()->json([
                'status' => 'paid',
                'message' => 'Payment already completed!',
            ]);
        }

        // Check status with Selcom
        $result = $this->selcom->checkOrderStatus(
            $restaurant->getSelcomCredentials(),
            $payment->transaction_reference
        );
        $paymentStatus = $this->selcom->parsePaymentStatus($result);

        if ($paymentStatus === 'paid') {
            $payment->update(['status' => 'paid']);
            $order->update(['status' => 'paid']);

            return response()->json([
                'status' => 'paid',
                'message' => 'Payment completed successfully!',
            ]);
        } elseif ($paymentStatus === 'failed') {
            $payment->update(['status' => 'failed']);

            return response()->json([
                'status' => 'failed',
                'message' => 'Payment failed or cancelled',
            ]);
        }

        return response()->json([
            'status' => 'pending',
            'message' => 'Waiting for payment confirmation...',
        ]);
    }

    public function export(Request $request)
    {
        $restaurant = Auth::user()->restaurant;

        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        [$start, $end] = $this->getDateRange($period, $startDate, $endDate);

        $payments = Payment::where('restaurant_id', $restaurant->id)
            ->with(['order', 'waiter'])
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['paid', 'completed'])
            ->orderBy('created_at', 'desc')
            ->get();

        $tips = \App\Models\Tip::where('restaurant_id', $restaurant->id)
            ->whereBetween('created_at', [$start, $end])
            ->with('waiter')
            ->get();

        $filename = 'revenue_report_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($payments, $tips) {
            $file = fopen('php://output', 'w');

            // Payments section
            fputcsv($file, ['PAYMENTS']);
            fputcsv($file, ['Date', config('salon.booking').' ID', config('salon.staff'), 'Amount (Tsh)', 'Method', 'Status']);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->created_at->format('Y-m-d H:i'),
                    $payment->order_id ?? 'N/A',
                    $payment->waiter?->name ?? 'Unassigned',
                    number_format($payment->amount, 2),
                    ucfirst($payment->method),
                    ucfirst($payment->status),
                ]);
            }

            // Empty row
            fputcsv($file, []);

            // Tips section
            fputcsv($file, ['TIPS']);
            fputcsv($file, ['Date', config('salon.booking').' ID', config('salon.staff'), 'Amount (Tsh)']);

            foreach ($tips as $tip) {
                fputcsv($file, [
                    $tip->created_at->format('Y-m-d H:i'),
                    $tip->order_id ?? 'N/A',
                    $tip->waiter?->name ?? 'Unassigned',
                    number_format($tip->amount, 2),
                ]);
            }

            // Summary
            fputcsv($file, []);
            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Revenue', number_format($payments->sum('amount'), 2)]);
            fputcsv($file, ['Total Tips', number_format($tips->sum('amount'), 2)]);
            fputcsv($file, ['Total Transactions', $payments->count()]);

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
