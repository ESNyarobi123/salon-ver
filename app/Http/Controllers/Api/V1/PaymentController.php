<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\SelcomService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected SelcomService $selcom;

    public function __construct(SelcomService $selcom)
    {
        $this->selcom = $selcom;
    }

    /**
     * Initiate USSD Push Payment for an Order
     */
    public function ussdRequest(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'phone_number' => 'required|string',
        ]);

        $order = Order::with('restaurant')->find($validated['order_id']);
        $restaurant = $order->restaurant;

        if (! $restaurant || ! $restaurant->hasSelcomConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway not configured for this saloon',
            ], 400);
        }

        $transactionRef = 'TXN-'.strtoupper(uniqid());

        $result = $this->selcom->initiatePayment($restaurant->getSelcomCredentials(), [
            'order_id' => $transactionRef,
            'email' => 'customer@taptap.co.tz',
            'name' => 'Customer',
            'phone' => $validated['phone_number'],
            'amount' => $order->total_amount,
            'description' => 'Booking #'.$order->id,
        ]);

        if (isset($result['status']) && $result['status'] === 'success') {
            $payment = Payment::create([
                'order_id' => $order->id,
                'restaurant_id' => $restaurant->id,
                'waiter_id' => $order->waiter_id,
                'customer_phone' => $validated['phone_number'],
                'amount' => $order->total_amount,
                'method' => 'ussd',
                'status' => 'pending',
                'transaction_reference' => $transactionRef,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'USSD push sent to '.$validated['phone_number'],
                'transaction_reference' => $transactionRef,
                'payment_id' => $payment->id,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to initiate payment',
        ], 400);
    }

    /**
     * Change notification before payment: get change to give when customer pays cash.
     * Call this before confirming cash payment so the app can show "Change to give: X Tsh".
     */
    public function cashChangeNotification(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount_received' => 'required|numeric|min:0',
        ]);

        $order = Order::find($validated['order_id']);
        $orderTotal = (float) $order->total_amount;
        $amountReceived = (float) $validated['amount_received'];
        $changeToGive = max(0, $amountReceived - $orderTotal);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_total' => $orderTotal,
            'amount_received' => $amountReceived,
            'change_to_give' => $changeToGive,
            'message' => $changeToGive > 0
                ? 'Change to give to customer: '.number_format($changeToGive).' Tsh'
                : ($amountReceived >= $orderTotal ? 'Exact amount or no change needed.' : 'Amount received is less than order total.'),
        ]);
    }

    /**
     * Record Cash Payment for an Order.
     * Optional amount_received: when provided, response includes change_to_give for notification/receipt.
     */
    public function cashPayment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount_received' => 'nullable|numeric|min:0',
        ]);

        $order = Order::with('restaurant')->find($validated['order_id']);
        $orderTotal = (float) $order->total_amount;
        $amountReceived = isset($validated['amount_received']) ? (float) $validated['amount_received'] : null;
        $changeToGive = $amountReceived !== null ? max(0, $amountReceived - $orderTotal) : null;

        $payment = Payment::create([
            'order_id' => $order->id,
            'restaurant_id' => $order->restaurant_id,
            'waiter_id' => $order->waiter_id,
            'amount' => $order->total_amount,
            'method' => 'cash',
            'status' => 'paid',
            'transaction_reference' => 'CASH-'.strtoupper(uniqid()),
        ]);

        $order->update(['status' => 'paid']);

        $response = [
            'success' => true,
            'payment' => $payment,
        ];
        if ($changeToGive !== null) {
            $response['change_to_give'] = $changeToGive;
            $response['order_total'] = $orderTotal;
            $response['amount_received'] = $amountReceived;
            $response['message'] = $changeToGive > 0
                ? 'Change to give to customer: '.number_format($changeToGive).' Tsh'
                : 'Payment recorded. No change needed.';
        }

        return response()->json($response);
    }

    /**
     * Check Payment Status (Polling)
     * This endpoint is called repeatedly by clients to check payment status
     */
    public function status(Order $order)
    {
        $order->load('restaurant');
        $payment = $order->payments()->where('method', 'ussd')->latest()->first();

        if ($payment && $payment->status === 'pending') {
            $restaurant = $order->restaurant;

            if ($restaurant && $restaurant->hasSelcomConfigured()) {
                $result = $this->selcom->checkOrderStatus(
                    $restaurant->getSelcomCredentials(),
                    $payment->transaction_reference
                );
                $paymentStatus = $this->selcom->parsePaymentStatus($result);

                if ($paymentStatus === 'paid') {
                    $payment->update(['status' => 'paid']);
                    $order->update(['status' => 'paid']);
                } elseif ($paymentStatus === 'failed') {
                    $payment->update(['status' => 'failed']);
                }
            }
        }

        return response()->json([
            'success' => true,
            'status' => $payment ? $payment->status : 'unpaid',
            'payment' => $payment,
        ]);
    }
}
