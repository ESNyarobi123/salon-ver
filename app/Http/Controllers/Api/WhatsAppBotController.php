<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Category;
use App\Models\CustomerRequest;
use App\Models\Feedback;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\Setting;
use App\Models\Table;
use App\Models\Tip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WhatsAppBotController extends Controller
{
    /**
     * Search for restaurants (Optimized for WhatsApp Buttons - Max 3 results)
     */
    public function searchRestaurant(Request $request)
    {
        $query = $request->input('query');

        $restaurants = Restaurant::where('name', 'like', "%{$query}%")
            ->where('is_active', true)
            ->limit(3)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'location' => $r->location,
                'support_phone' => $r->getCustomerSupportPhone(),
            ]);

        return response()->json([
            'success' => true,
            'count' => $restaurants->count(),
            'data' => $restaurants->values(),
        ]);
    }

    /**
     * Verify Restaurant and Table (For QR Scan Entry)
     */
    public function verifyRestaurant(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
        ]);

        $restaurant = Restaurant::find($request->restaurant_id);

        if (! $restaurant || ! $restaurant->is_active) {
            return response()->json(['success' => false, 'message' => 'Saloon not found or inactive'], 404);
        }

        // Check if waiter_id or table_id is provided
        $waiterId = $request->input('waiter_id');
        $tableId = $request->input('table_id');
        $waiter = null;
        $table = null;

        if ($waiterId) {
            $waiter = User::where('id', $waiterId)
                ->where('restaurant_id', $restaurant->id)
                ->first();
        }

        if ($tableId) {
            $table = Table::withoutGlobalScopes()
                ->where('id', $tableId)
                ->where('restaurant_id', $restaurant->id)
                ->first();
        }

        return response()->json([
            'success' => true,
            'skip_standalone_welcome' => true,
            'data' => [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'location' => $restaurant->location,
                'support_phone' => $restaurant->getCustomerSupportPhone(),
                'table_number' => $table ? $table->name : $request->input('table_number'),
                'table_id' => $table ? $table->id : null,
                'table_tag' => $table ? $table->table_tag : null,
                'waiter_id' => $waiter ? $waiter->id : null,
                'waiter_name' => $waiter ? $waiter->name : null,
                'waiter_code' => $waiter ? $waiter->waiter_code : null,
            ],
        ]);
    }

    /**
     * Verify service tag: PREFIX-T## (seat) or PREFIX-W## (stylist at that saloon).
     * TipTap-wide stylist account IDs (8-hex, legacy 4-digit, or TIPTAP-W-#####) are handled in parseEntry(), not here.
     */
    public function verifyTag(Request $request)
    {
        $request->validate([
            'tag' => 'required|string|max:20',
        ]);

        $tag = strtoupper(trim($request->input('tag')));

        // Determine if it's a table tag or waiter code
        // Format: PREFIX-T## for tables, PREFIX-W## for waiters
        if (preg_match('/^([A-Z0-9]+)-T(\d+)$/i', $tag, $matches)) {
            // Table tag
            $prefix = $matches[1];

            $table = Table::withoutGlobalScopes()
                ->where('table_tag', $tag)
                ->with('restaurant')
                ->first();

            if (! $table || ! $table->restaurant || ! $table->restaurant->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid seat tag or saloon inactive',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'type' => 'table',
                'skip_standalone_welcome' => true,
                'data' => [
                    'restaurant_id' => $table->restaurant->id,
                    'restaurant_name' => $table->restaurant->name,
                    'restaurant_location' => $table->restaurant->location,
                    'support_phone' => $table->restaurant->getCustomerSupportPhone(),
                    'table_id' => $table->id,
                    'table_name' => $table->name,
                    'table_tag' => $table->table_tag,
                    'waiter_id' => null,
                    'waiter_name' => null,
                ],
            ]);

        } elseif (preg_match('/^([A-Z0-9]+)-W(\d+)$/i', $tag, $matches)) {
            // Waiter code
            $prefix = $matches[1];

            $waiter = User::where('waiter_code', $tag)
                ->with('restaurant')
                ->first();

            if (! $waiter || ! $waiter->restaurant || ! $waiter->restaurant->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid stylist code or saloon inactive',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'type' => 'waiter',
                'skip_standalone_welcome' => true,
                'data' => [
                    'restaurant_id' => $waiter->restaurant->id,
                    'restaurant_name' => $waiter->restaurant->name,
                    'restaurant_location' => $waiter->restaurant->location,
                    'support_phone' => $waiter->restaurant->getCustomerSupportPhone(),
                    'table_id' => null,
                    'table_name' => null,
                    'table_tag' => null,
                    'waiter_id' => $waiter->id,
                    'waiter_name' => $waiter->name,
                    'waiter_code' => $waiter->waiter_code,
                ],
            ]);

        } else {
            // Try to find by prefix only (might be a restaurant tag)
            $restaurant = Restaurant::where('tag_prefix', $tag)
                ->where('is_active', true)
                ->first();

            if ($restaurant) {
                return response()->json([
                    'success' => true,
                    'type' => 'restaurant',
                    'skip_standalone_welcome' => true,
                    'data' => [
                        'restaurant_id' => $restaurant->id,
                        'restaurant_name' => $restaurant->name,
                        'restaurant_location' => $restaurant->location,
                        'support_phone' => $restaurant->getCustomerSupportPhone(),
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid tag. Use PREFIX-T## (seat) or PREFIX-W## (stylist at that saloon). For TipTap stylist ID (8-hex, legacy 4-digit, or TIPTAP-W-#####), use parse-entry as plain text.',
            ], 400);
        }
    }

    /**
     * Parse QR code or tag input and return appropriate data
     * Handles: START_2, START_2_T5, START_2_S3 (stylist), legacy START_2_W3, 8-hex / legacy 4-digit TipTap ID, TIPTAP-W-00001, SMK-T01, SMK-W01
     */
    public function parseEntry(Request $request)
    {
        // Support both POST body and query parameter for flexibility
        $inputValue = $request->input('input') ?? $request->query('input') ?? $request->query('text');

        if (empty($inputValue)) {
            return response()->json([
                'success' => false,
                'message' => 'The input field is required.',
                'hint' => 'Send input as POST body: {"input": "START_2_S12"} or ?input=09E26870',
            ], 422);
        }

        $input = strtoupper(trim($inputValue));

        // Pattern 1: START_{restaurant_id}
        if (preg_match('/^START_(\d+)$/i', $input, $matches)) {
            $restaurant = Restaurant::find($matches[1]);
            if (! $restaurant || ! $restaurant->is_active) {
                return response()->json(['success' => false, 'message' => 'Saloon not found'], 404);
            }

            return response()->json([
                'success' => true,
                'type' => 'restaurant',
                'skip_standalone_welcome' => true,
                'data' => [
                    'restaurant_id' => $restaurant->id,
                    'restaurant_name' => $restaurant->name,
                    'support_phone' => $restaurant->getCustomerSupportPhone(),
                ],
            ]);
        }

        // Pattern 2: START_{restaurant_id}_T{table_id}
        if (preg_match('/^START_(\d+)_T(\d+)$/i', $input, $matches)) {
            $restaurant = Restaurant::find($matches[1]);
            $table = Table::withoutGlobalScopes()->find($matches[2]);

            if (! $restaurant || ! $restaurant->is_active) {
                return response()->json(['success' => false, 'message' => 'Saloon not found'], 404);
            }

            return response()->json([
                'success' => true,
                'type' => 'table',
                'skip_standalone_welcome' => true,
                'data' => [
                    'restaurant_id' => $restaurant->id,
                    'restaurant_name' => $restaurant->name,
                    'support_phone' => $restaurant->getCustomerSupportPhone(),
                    'table_id' => $table ? $table->id : null,
                    'table_name' => $table ? $table->name : null,
                    'table_tag' => $table ? $table->table_tag : null,
                ],
            ]);
        }

        // Pattern 3a: START_{restaurant_id}_S{user_id} — stylist deep link (current QR format)
        if (preg_match('/^START_(\d+)_S(\d+)$/i', $input, $matches)) {
            $restaurant = Restaurant::find($matches[1]);
            $waiter = User::find($matches[2]);

            if (! $restaurant || ! $restaurant->is_active) {
                return response()->json(['success' => false, 'message' => 'Saloon not found'], 404);
            }

            return response()->json([
                'success' => true,
                'type' => 'waiter',
                'skip_standalone_welcome' => true,
                'data' => [
                    'restaurant_id' => $restaurant->id,
                    'restaurant_name' => $restaurant->name,
                    'support_phone' => $restaurant->getCustomerSupportPhone(),
                    'waiter_id' => $waiter ? $waiter->id : null,
                    'waiter_name' => $waiter ? $waiter->name : null,
                    'waiter_code' => $waiter ? $waiter->waiter_code : null,
                ],
            ]);
        }

        // Pattern 3b: Legacy START_{restaurant_id}_W{user_id} (old printed QRs)
        if (preg_match('/^START_(\d+)_W(\d+)$/i', $input, $matches)) {
            $restaurant = Restaurant::find($matches[1]);
            $waiter = User::find($matches[2]);

            if (! $restaurant || ! $restaurant->is_active) {
                return response()->json(['success' => false, 'message' => 'Saloon not found'], 404);
            }

            return response()->json([
                'success' => true,
                'type' => 'waiter',
                'skip_standalone_welcome' => true,
                'data' => [
                    'restaurant_id' => $restaurant->id,
                    'restaurant_name' => $restaurant->name,
                    'support_phone' => $restaurant->getCustomerSupportPhone(),
                    'waiter_id' => $waiter ? $waiter->id : null,
                    'waiter_name' => $waiter ? $waiter->name : null,
                    'waiter_code' => $waiter ? $waiter->waiter_code : null,
                ],
            ]);
        }

        // Pattern 3c: Global TipTap stylist ID (typed or scanned as plain text)
        if (preg_match('/^TIPTAP-W-\d+$/i', $input)) {
            $waiter = User::role('waiter')
                ->where('global_waiter_number', strtoupper($input))
                ->with('restaurant')
                ->first();

            if (! $waiter || ! $waiter->restaurant_id || ! $waiter->restaurant || ! $waiter->restaurant->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stylist not found or not linked to an active saloon',
                ], 404);
            }

            $restaurant = $waiter->restaurant;

            return response()->json([
                'success' => true,
                'type' => 'waiter',
                'skip_standalone_welcome' => true,
                'data' => [
                    'restaurant_id' => $restaurant->id,
                    'restaurant_name' => $restaurant->name,
                    'support_phone' => $restaurant->getCustomerSupportPhone(),
                    'waiter_id' => $waiter->id,
                    'waiter_name' => $waiter->name,
                    'waiter_code' => $waiter->waiter_code,
                ],
            ]);
        }

        // Pattern 3d: 8-char hex global stylist ID (current format)
        if (preg_match('/^[0-9A-F]{8}$/', $input)) {
            $waiter = User::role('waiter')
                ->where('global_waiter_number', $input)
                ->with('restaurant')
                ->first();

            if (! $waiter || ! $waiter->restaurant_id || ! $waiter->restaurant || ! $waiter->restaurant->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stylist not found or not linked to an active saloon',
                ], 404);
            }

            $restaurant = $waiter->restaurant;

            return response()->json([
                'success' => true,
                'type' => 'waiter',
                'skip_standalone_welcome' => true,
                'data' => [
                    'restaurant_id' => $restaurant->id,
                    'restaurant_name' => $restaurant->name,
                    'support_phone' => $restaurant->getCustomerSupportPhone(),
                    'waiter_id' => $waiter->id,
                    'waiter_name' => $waiter->name,
                    'waiter_code' => $waiter->waiter_code,
                ],
            ]);
        }

        // Pattern 3e: legacy 4-digit global stylist ID (0000–9999)
        if (preg_match('/^\d{1,4}$/', trim($input))) {
            $code = User::normalizeGlobalWaiterNumberForLookup($input);
            $waiter = User::role('waiter')
                ->where('global_waiter_number', $code)
                ->with('restaurant')
                ->first();

            if (! $waiter || ! $waiter->restaurant_id || ! $waiter->restaurant || ! $waiter->restaurant->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stylist not found or not linked to an active saloon',
                ], 404);
            }

            $restaurant = $waiter->restaurant;

            return response()->json([
                'success' => true,
                'type' => 'waiter',
                'skip_standalone_welcome' => true,
                'data' => [
                    'restaurant_id' => $restaurant->id,
                    'restaurant_name' => $restaurant->name,
                    'support_phone' => $restaurant->getCustomerSupportPhone(),
                    'waiter_id' => $waiter->id,
                    'waiter_name' => $waiter->name,
                    'waiter_code' => $waiter->waiter_code,
                ],
            ]);
        }

        // Pattern 4: Service tags (SMK-T01, SMK-W01)
        if (preg_match('/^([A-Z0-9]+)-(T|W)(\d+)$/i', $input)) {
            return $this->verifyTag(new Request(['tag' => $input]));
        }

        // Pattern 5: Old format START_{restaurant_id}_{table_number} (backward compatibility)
        if (preg_match('/^START_(\d+)_(\d+)$/i', $input, $matches)) {
            $restaurant = Restaurant::find($matches[1]);
            if (! $restaurant || ! $restaurant->is_active) {
                return response()->json(['success' => false, 'message' => 'Saloon not found'], 404);
            }

            // Treat second number as table_number (old format)
            return response()->json([
                'success' => true,
                'type' => 'table',
                'skip_standalone_welcome' => true,
                'data' => [
                    'restaurant_id' => $restaurant->id,
                    'restaurant_name' => $restaurant->name,
                    'support_phone' => $restaurant->getCustomerSupportPhone(),
                    'table_number' => $matches[2],
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid input format',
        ], 400);
    }

    /**
     * Get Categories for a Restaurant
     */
    public function getCategories($restaurantId)
    {
        $categories = Category::withoutGlobalScopes()->where('restaurant_id', $restaurantId)
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get Menu Items for a Category
     */
    public function getCategoryItems($categoryId)
    {
        $items = MenuItem::withoutGlobalScopes()->where('category_id', $categoryId)
            ->where('is_available', true)
            ->get(['id', 'name', 'price', 'description', 'image'])
            ->map(function ($item) {
                $item->imageUrl = $item->imageUrl();

                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * Get Single Item Details
     */
    public function getItemDetails($itemId)
    {
        $item = MenuItem::withoutGlobalScopes()->with(['category' => function ($query) {
            $query->withoutGlobalScopes();
        }])->find($itemId);

        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Service not found'], 404);
        }

        $item->imageUrl = $item->imageUrl();

        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }

    /**
     * Get Full Menu (Categories + Items) for a Restaurant
     */
    public function getFullMenu($restaurantId)
    {
        $categories = Category::withoutGlobalScopes()->with(['menuItems' => function ($query) {
            $query->withoutGlobalScopes()->where('is_available', true);
        }])->where('restaurant_id', $restaurantId)->get()->map(function ($category) {
            $category->imageUrl = $category->imageUrl();
            $category->menuItems->map(function ($item) {
                $item->imageUrl = $item->imageUrl();

                return $item;
            });

            return $category;
        });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Create Order from Bot
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'table_id' => 'nullable|exists:tables,id',
            'table_number' => 'nullable|string',
            'waiter_id' => 'nullable|exists:users,id',
            'customer_phone' => 'required',
            'customer_name' => 'nullable|string',
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Get table number from table_id if provided
        $tableNumber = $request->table_number;
        if (! $tableNumber && $request->table_id) {
            $table = Table::withoutGlobalScopes()->find($request->table_id);
            $tableNumber = $table ? $table->name : null;
        }

        try {
            return DB::transaction(function () use ($request, $tableNumber) {
                $totalAmount = 0;
                $orderItems = [];

                foreach ($request->items as $itemData) {
                    $menuItem = MenuItem::withoutGlobalScopes()->find($itemData['menu_item_id']);
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

                $order = Order::withoutGlobalScopes()->create([
                    'restaurant_id' => $request->restaurant_id,
                    'waiter_id' => $request->waiter_id,
                    'table_number' => $tableNumber,
                    'customer_phone' => $request->customer_phone,
                    'customer_name' => $request->customer_name,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                ]);

                foreach ($orderItems as $item) {
                    $order->items()->create($item);
                }

                // Log Activity
                Activity::create([
                    'description' => "New WhatsApp order #{$order->id} from {$request->customer_phone}",
                    'type' => 'order_created',
                    'properties' => [
                        'order_id' => $order->id,
                        'source' => 'whatsapp',
                        'waiter_id' => $request->waiter_id,
                    ],
                ]);

                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'total' => $totalAmount,
                    'waiter_id' => $request->waiter_id,
                    'table_number' => $tableNumber,
                    'message' => 'Booking created successfully',
                ]);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Check Order & Payment Status (Polling)
     * Bot calls this repeatedly to check if payment is complete
     */
    public function getOrderStatus($orderId)
    {
        $order = Order::withoutGlobalScopes()->with(['restaurant', 'items.menuItem' => function ($query) {
            $query->withoutGlobalScopes();
        }, 'payments'])->find($orderId);

        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $payment = $order->payments()->where('method', 'ussd')->latest()->first();

        // If already completed or failed, return immediately
        if ($payment && in_array($payment->status, ['paid', 'failed', 'cancelled'])) {
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_status' => $order->status,
                'payment_status' => $payment->status,
                'is_paid' => $payment->status === 'paid',
                'is_failed' => in_array($payment->status, ['failed', 'cancelled']),
                'total' => $order->total_amount,
                'items' => $order->items,
            ]);
        }

        // Polling logic for Selcom - check with Selcom API
        if ($payment && $payment->status === 'pending') {
            $restaurant = $order->restaurant;

            if ($restaurant && $restaurant->hasSelcomConfigured()) {
                $selcom = new \App\Services\SelcomService;
                $result = $selcom->checkOrderStatus(
                    $restaurant->getSelcomCredentials(),
                    $payment->transaction_reference
                );
                $paymentStatus = $selcom->parsePaymentStatus($result);

                if ($paymentStatus === 'paid') {
                    $payment->update(['status' => 'paid']);
                    $order->update(['status' => 'paid']);

                    // Log successful payment
                    Activity::create([
                        'description' => 'Booking #'.$order->id.' payment completed: Tsh '.number_format($order->total_amount),
                        'type' => 'order_payment_success',
                        'properties' => [
                            'order_id' => $order->id,
                            'payment_id' => $payment->id,
                            'amount' => $order->total_amount,
                        ],
                    ]);

                    // Refresh to get updated status
                    $payment->refresh();
                    $order->refresh();
                } elseif ($paymentStatus === 'failed') {
                    $payment->update(['status' => 'failed']);
                    $payment->refresh();
                }
            }
        }

        // Check if payment has expired (older than 10 minutes)
        if ($payment && $payment->status === 'pending' && $payment->created_at->diffInMinutes(now()) > 10) {
            $payment->update(['status' => 'failed']);
            $payment->refresh();
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_status' => $order->status,
            'payment_status' => $payment ? $payment->status : 'unpaid',
            'is_paid' => $payment && $payment->status === 'paid',
            'is_failed' => $payment && in_array($payment->status, ['failed', 'cancelled']),
            'is_pending' => $payment && $payment->status === 'pending',
            'total' => $order->total_amount,
            'items' => $order->items,
            'transaction_reference' => $payment?->transaction_reference,
        ]);
    }

    /**
     * Submit Feedback from Bot
     */
    public function submitFeedback(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'order_id' => 'nullable|exists:orders,id',
            'waiter_id' => 'nullable|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $data = $request->all();

        // If waiter_id is not provided but order_id is, try to get waiter_id from order
        if (empty($data['waiter_id']) && ! empty($data['order_id'])) {
            $order = Order::withoutGlobalScopes()->find($data['order_id']);
            if ($order && $order->waiter_id) {
                $data['waiter_id'] = $order->waiter_id;
            }
        }

        $feedback = Feedback::withoutGlobalScopes()->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Thanks — feedback received.',
        ]);
    }

    /**
     * Submit Tip from Bot
     */
    public function submitTip(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            // Get waiter_id from order if exists
            $order = Order::withoutGlobalScopes()->find($request->order_id);
            $waiterId = $order ? $order->waiter_id : null;

            $tip = Tip::withoutGlobalScopes()->create([
                'restaurant_id' => $request->restaurant_id,
                'order_id' => $request->order_id,
                'waiter_id' => $waiterId,
                'amount' => $request->amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tip received — thank you.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit tip: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Initiate USSD Payment
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'phone_number' => 'required',
            'amount' => 'required|numeric',
            'network' => 'nullable|string',
        ]);

        $order = Order::withoutGlobalScopes()->with('restaurant')->find($request->order_id);
        $restaurant = $order->restaurant;

        $transactionId = 'BOT-'.$order->id.'-'.time();

        if (Setting::get('demo_push', '0') === '1') {
            $payment = Payment::create([
                'order_id' => $order->id,
                'restaurant_id' => $order->restaurant_id,
                'waiter_id' => $order->waiter_id,
                'amount' => $request->amount,
                'method' => 'ussd',
                'status' => 'paid',
                'transaction_reference' => $transactionId,
            ]);
            $order->update(['status' => 'paid']);

            return response()->json([
                'success' => true,
                'payment_id' => $payment->id,
                'message' => 'Demo: Payment marked successful (no push sent)',
            ]);
        }

        if (! $restaurant || ! $restaurant->hasSelcomConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Saloon payment gateway not configured',
            ], 400);
        }

        $selcom = new \App\Services\SelcomService;
        $result = $selcom->initiatePayment($restaurant->getSelcomCredentials(), [
            'order_id' => $transactionId,
            'email' => $order->customer_phone.'@taptap.com',
            'name' => 'WhatsApp '.config('salon.customer'),
            'phone' => $request->phone_number,
            'amount' => $request->amount,
            'description' => 'Booking #'.$order->id,
        ]);

        if (isset($result['status']) && $result['status'] === 'success') {
            $payment = Payment::create([
                'order_id' => $order->id,
                'restaurant_id' => $order->restaurant_id,
                'waiter_id' => $order->waiter_id,
                'amount' => $request->amount,
                'method' => 'ussd',
                'status' => 'pending',
                'transaction_reference' => $transactionId,
            ]);

            return response()->json([
                'success' => true,
                'payment_id' => $payment->id,
                'message' => 'USSD Prompt sent to '.$request->phone_number,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to initiate payment with Selcom',
            'debug' => $result,
        ], 400);
    }

    /**
     * Initiate Quick Payment (without order)
     * Allows payment of any amount with a description
     */
    public function initiateQuickPayment(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'phone_number' => 'required',
            'amount' => 'required|numeric|min:100',
            'description' => 'required|string|max:500',
            'network' => 'nullable|string',
            'waiter_id' => 'nullable|exists:users,id',
        ]);

        $restaurant = Restaurant::find($request->restaurant_id);

        if (! $restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Saloon not found',
            ], 404);
        }

        $transactionId = 'QUICK-'.$restaurant->id.'-'.time();

        if (Setting::get('demo_push', '0') === '1') {
            $paymentData = [
                'restaurant_id' => $restaurant->id,
                'customer_phone' => $request->phone_number,
                'amount' => $request->amount,
                'method' => 'ussd',
                'payment_type' => 'quick',
                'status' => 'paid',
                'transaction_reference' => $transactionId,
                'description' => $request->description,
            ];
            if (Schema::hasColumn('payments', 'waiter_id') && $request->waiter_id) {
                $paymentData['waiter_id'] = $request->waiter_id;
            }
            $payment = Payment::create($paymentData);

            if ($payment->waiter_id) {
                $tipData = [
                    'restaurant_id' => $payment->restaurant_id,
                    'waiter_id' => $payment->waiter_id,
                    'order_id' => null,
                    'amount' => $payment->amount,
                ];
                if (Schema::hasColumn('tips', 'payment_id')) {
                    $tipData['payment_id'] = $payment->id;
                    Tip::withoutGlobalScopes()->firstOrCreate(
                        ['payment_id' => $payment->id],
                        $tipData
                    );
                } else {
                    Tip::withoutGlobalScopes()->create($tipData);
                }
            }

            Activity::create([
                'description' => 'Demo: Quick payment completed: Tsh '.number_format($request->amount),
                'type' => 'payment_success',
                'properties' => [
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount,
                    'phone' => $request->phone_number,
                ],
            ]);

            return response()->json([
                'success' => true,
                'payment_id' => $payment->id,
                'message' => 'Demo: Payment marked successful (no push sent)',
                'description' => $request->description,
            ]);
        }

        if (! $restaurant->hasSelcomConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Saloon payment gateway not configured',
            ], 400);
        }

        $selcom = new \App\Services\SelcomService;
        $result = $selcom->initiatePayment($restaurant->getSelcomCredentials(), [
            'order_id' => $transactionId,
            'email' => $request->phone_number.'@taptap.com',
            'name' => 'WhatsApp '.config('salon.customer'),
            'phone' => $request->phone_number,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        if (isset($result['status']) && $result['status'] === 'success') {
            $paymentData = [
                'restaurant_id' => $restaurant->id,
                'customer_phone' => $request->phone_number,
                'amount' => $request->amount,
                'method' => 'ussd',
                'payment_type' => 'quick',
                'status' => 'pending',
                'transaction_reference' => $transactionId,
                'description' => $request->description,
            ];
            if (Schema::hasColumn('payments', 'waiter_id') && $request->waiter_id) {
                $paymentData['waiter_id'] = $request->waiter_id;
            }
            $payment = Payment::create($paymentData);

            Activity::create([
                'description' => 'Quick payment initiated: Tsh '.number_format($request->amount)." from {$request->phone_number}",
                'type' => 'quick_payment',
                'properties' => [
                    'payment_id' => $payment->id,
                    'source' => 'whatsapp',
                    'description' => $request->description,
                ],
            ]);

            return response()->json([
                'success' => true,
                'payment_id' => $payment->id,
                'message' => 'USSD Prompt sent to '.$request->phone_number,
                'description' => $request->description,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to initiate payment with Selcom',
            'debug' => $result,
        ], 400);
    }

    /**
     * Check Quick Payment Status (Polling)
     * Bot calls this repeatedly to check if payment is complete
     */
    public function getQuickPaymentStatus($paymentId)
    {
        $payment = Payment::where('id', $paymentId)
            ->where('payment_type', 'quick')
            ->with('restaurant')
            ->first();

        if (! $payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }

        // If already completed or failed, return immediately
        if (in_array($payment->status, ['paid', 'failed', 'cancelled'])) {
            return response()->json([
                'success' => true,
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'is_complete' => $payment->status === 'paid',
                'is_failed' => in_array($payment->status, ['failed', 'cancelled']),
                'amount' => $payment->amount,
                'description' => $payment->description,
                'customer_phone' => $payment->customer_phone,
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        // Polling logic for Selcom - check with Selcom API
        if ($payment->status === 'pending' && $payment->restaurant) {
            $restaurant = $payment->restaurant;

            if ($restaurant->hasSelcomConfigured()) {
                $selcom = new \App\Services\SelcomService;
                $result = $selcom->checkOrderStatus(
                    $restaurant->getSelcomCredentials(),
                    $payment->transaction_reference
                );
                $paymentStatus = $selcom->parsePaymentStatus($result);

                // Update payment status based on Selcom response
                if ($paymentStatus === 'paid') {
                    $payment->update(['status' => 'paid']);

                    // If this quick payment was a tip (waiter_id set), create Tip so waiter sees it in API
                    if ($payment->waiter_id) {
                        $tipData = [
                            'restaurant_id' => $payment->restaurant_id,
                            'waiter_id' => $payment->waiter_id,
                            'order_id' => null,
                            'amount' => $payment->amount,
                        ];
                        if (Schema::hasColumn('tips', 'payment_id')) {
                            $tipData['payment_id'] = $payment->id;
                            Tip::withoutGlobalScopes()->firstOrCreate(
                                ['payment_id' => $payment->id],
                                $tipData
                            );
                        } else {
                            Tip::withoutGlobalScopes()->create($tipData);
                        }
                    }

                    // Log successful payment
                    Activity::create([
                        'description' => 'Quick payment completed: Tsh '.number_format($payment->amount),
                        'type' => 'payment_success',
                        'properties' => [
                            'payment_id' => $payment->id,
                            'amount' => $payment->amount,
                            'phone' => $payment->customer_phone,
                        ],
                    ]);
                } elseif ($paymentStatus === 'failed') {
                    $payment->update(['status' => 'failed']);
                }

                // Refresh payment to get updated status
                $payment->refresh();
            }
        }

        // Check if payment has expired (older than 10 minutes)
        $isExpired = $payment->status === 'pending'
            && $payment->created_at->diffInMinutes(now()) > 10;

        if ($isExpired) {
            $payment->update(['status' => 'failed']);
            $payment->refresh();
        }

        return response()->json([
            'success' => true,
            'payment_id' => $payment->id,
            'status' => $payment->status,
            'is_complete' => $payment->status === 'paid',
            'is_failed' => in_array($payment->status, ['failed', 'cancelled']),
            'is_pending' => $payment->status === 'pending',
            'amount' => $payment->amount,
            'description' => $payment->description,
            'customer_phone' => $payment->customer_phone,
            'transaction_reference' => $payment->transaction_reference,
            'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get Tables for a Restaurant
     */
    public function getTables($restaurantId)
    {
        $tables = Table::withoutGlobalScopes()->where('restaurant_id', $restaurantId)
            ->where('is_active', true)
            ->get(['id', 'name', 'capacity']);

        return response()->json([
            'success' => true,
            'data' => $tables,
        ]);
    }

    /**
     * Check if a waiter is online (for bot: before showing "call waiter" or sending request).
     * GET /api/bot/waiter/{waiterId}/status
     */
    public function waiterStatus(string $waiterId)
    {
        $waiter = User::role('waiter')->find($waiterId);

        if (! $waiter) {
            return response()->json([
                'success' => false,
                'message' => 'Stylist not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'waiter_id' => (int) $waiter->id,
                'name' => $waiter->name,
                'is_online' => (bool) $waiter->is_online,
                'last_online_at' => $waiter->last_online_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Call Waiter from Bot
     */
    public function callWaiter(Request $request)
    {
        // Handle both 'type' and 'request_type' (from bot)
        $type = $request->input('type') ?? $request->input('request_type');

        // Map bot values to DB values
        if (in_array($type, ['Call Waiter', 'Call stylist'], true)) {
            $type = 'call_waiter';
        }
        if (in_array($type, ['Request Bill', 'Request bill'], true)) {
            $type = 'request_bill';
        }

        $request->merge(['type' => $type]);

        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'table_number' => 'nullable|string|max:50',
            'table_id' => 'nullable|exists:tables,id',
            'waiter_id' => 'nullable|exists:users,id',
            'type' => 'required|in:call_waiter,request_bill',
        ]);

        // Get table info if table_id is provided but table_number is not
        $tableNumber = $request->table_number;
        $table = $request->table_id ? Table::withoutGlobalScopes()->find($request->table_id) : null;
        if (empty($tableNumber) && $table) {
            $tableNumber = $table->name;
        }

        // Waiter: from request or from table's linked waiter (only if that waiter is online)
        $waiterId = $request->waiter_id;
        if (! $waiterId && $table && $table->waiter_id) {
            $tableWaiter = User::find($table->waiter_id);
            if ($tableWaiter && $tableWaiter->is_online) {
                $waiterId = $table->waiter_id;
            }
        }
        if ($waiterId) {
            $waiterForRequest = User::find($waiterId);
            if (! $waiterForRequest || ! $waiterForRequest->is_online) {
                $waiterId = null;
            }
        }

        // Get waiter info if waiter_id is set
        $waiterName = null;
        if ($waiterId) {
            $waiter = User::find($waiterId);
            $waiterName = $waiter ? $waiter->name : null;
        }

        $customerRequest = CustomerRequest::withoutGlobalScopes()->create([
            'restaurant_id' => $request->restaurant_id,
            'table_number' => $tableNumber,
            'table_id' => $request->table_id,
            'waiter_id' => $waiterId,
            'type' => $request->type,
            'status' => 'pending',
        ]);

        $stylist = config('salon.staff');
        $message = $request->type === 'request_bill'
            ? 'Receipt request sent'
            : "{$stylist} notified";
        if ($waiterName) {
            $message = $request->type === 'request_bill'
                ? "Receipt request sent to {$waiterName}"
                : "{$waiterName} ({$stylist}) has been notified";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'request_id' => $customerRequest->id,
                'table_number' => $tableNumber,
                'waiter_name' => $waiterName,
            ],
        ]);
    }

    /**
     * Get Waiters for a Restaurant
     */
    public function getWaiters($restaurantId)
    {
        $waiters = User::role('waiter')
            ->where('restaurant_id', $restaurantId)
            ->get(['id', 'name', 'is_online']);

        return response()->json([
            'success' => true,
            'data' => $waiters->map(fn ($w) => [
                'id' => $w->id,
                'name' => $w->name,
                'is_online' => (bool) $w->is_online,
            ]),
        ]);
    }

    /**
     * Get Active Order (Bill) for a Table
     */
    public function getActiveOrder(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'table_number' => 'required',
        ]);

        $order = Order::withoutGlobalScopes()
            ->where('restaurant_id', $request->restaurant_id)
            ->where('table_number', $request->table_number)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->with([
                'items.menuItem' => function ($query) {
                    $query->withoutGlobalScopes();
                },
                'waiter',
                'payments' => fn ($q) => $q->latest(),
            ])
            ->latest()
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'No active booking found for this seat',
            ], 404);
        }

        $latestPayment = $order->payments->first();
        $paymentStatus = $latestPayment ? $latestPayment->status : 'unpaid';

        $items = $order->items->map(function ($item) {
            return [
                'name' => $item->name ?? ($item->menuItem ? $item->menuItem->name : 'Custom item'),
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->total,
            ];
        });

        $orderPayload = [
            'id' => $order->id,
            'total' => (float) $order->total_amount,
            'status' => $order->status,
            'payment_status' => $paymentStatus,
            'waiter_name' => $order->waiter?->name,
            'items' => $items,
        ];

        return response()->json([
            'success' => true,
            /** @deprecated Bot should prefer `order`; kept for older clients */
            'data' => [
                'order_id' => $order->id,
                'total' => $order->total_amount,
                'status' => $order->status,
                'payment_status' => $paymentStatus,
                'waiter_name' => $order->waiter?->name,
                'items' => $items,
            ],
            'order' => $orderPayload,
        ]);
    }

    /**
     * Get Menu Image for a Restaurant
     */
    public function getMenuImage($restaurantId)
    {
        $restaurant = Restaurant::find($restaurantId);

        if (! $restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Saloon not found',
            ], 404);
        }

        if (! $restaurant->menu_image) {
            return response()->json([
                'success' => false,
                'message' => 'No service menu image available for this saloon',
            ], 404);
        }

        // Fetch URL for menu image (path: storage/app/public/menu_images)
        $imageUrl = $restaurant->menuImageUrl();

        return response()->json([
            'success' => true,
            'data' => [
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name,
                'menu_image_url' => $imageUrl,
            ],
        ]);
    }

    /**
     * Guest Wi‑Fi credentials for the WhatsApp bot (manager-configured).
     */
    public function getGuestWifi($restaurantId)
    {
        $restaurant = Restaurant::find($restaurantId);

        if (! $restaurant || ! $restaurant->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Saloon not found or inactive',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'wifi_ssid' => $restaurant->guest_wifi_ssid,
                'wifi_password' => $restaurant->guest_wifi_password,
            ],
        ]);
    }

    /**
     * Create Order from Text (Natural Language)
     * table_id/table_number is optional - order can be created via waiter QR scan
     */
    public function createOrderByText(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'table_id' => 'nullable|exists:tables,id',
            'table_number' => 'nullable|string',
            'waiter_id' => 'nullable|exists:users,id',
            'customer_phone' => 'required',
            'customer_name' => 'nullable|string',
            'order_text' => 'required|string',
        ]);

        $text = $request->order_text;
        $restaurantId = $request->restaurant_id;

        // Get table number from table_id if provided
        $tableNumber = $request->table_number;
        if (! $tableNumber && $request->table_id) {
            $table = Table::withoutGlobalScopes()->find($request->table_id);
            $tableNumber = $table ? $table->name : null;
        }

        // Fetch all available items for this restaurant
        $menuItems = MenuItem::withoutGlobalScopes()
            ->where('restaurant_id', $restaurantId)
            ->where('is_available', true)
            ->get();

        // Sort by name length descending to match longest names first (e.g. "Chips Mayai" before "Chips")
        $sortedItems = $menuItems->sortByDesc(function ($item) {
            return strlen($item->name);
        });

        $matchedItems = [];
        $totalAmount = 0;

        foreach ($sortedItems as $item) {
            // Escape special characters in item name for regex
            $escapedName = preg_quote($item->name, '/');

            // Regex to find item name, optionally preceded OR followed by a number (quantity)
            // Matches: "2 Chips", "Chips 2", "2x Chips", "Chips x2"
            $regex = '/(?:(\d+)\s*[xX]?\s*)?\b'.$escapedName.'\b(?:\s*[xX]?\s*(\d+))?/i';

            if (preg_match($regex, $text, $matches)) {
                $q1 = isset($matches[1]) && $matches[1] !== '' ? (int) $matches[1] : 0;
                $q2 = isset($matches[2]) && $matches[2] !== '' ? (int) $matches[2] : 0;
                $quantity = max($q1, $q2, 1);

                $matchedItems[] = [
                    'menu_item_id' => $item->id,
                    'quantity' => $quantity,
                    'price' => $item->price,
                    'name' => $item->name, // For response and DB
                    'subtotal' => $item->price * $quantity,
                ];

                $totalAmount += $item->price * $quantity;

                // Remove the matched part from text to prevent double matching
                // We replace with spaces to preserve word boundaries for other matches
                $text = str_replace($matches[0], str_repeat(' ', strlen($matches[0])), $text);
            }
        }

        // If no items matched, create a custom order with the raw text
        if (empty($matchedItems)) {
            try {
                return DB::transaction(function () use ($request, $tableNumber) {
                    $order = Order::withoutGlobalScopes()->create([
                        'restaurant_id' => $request->restaurant_id,
                        'waiter_id' => $request->waiter_id,
                        'table_number' => $tableNumber,
                        'customer_phone' => $request->customer_phone,
                        'customer_name' => $request->customer_name,
                        'total_amount' => 0, // Unknown price
                        'status' => 'pending',
                        'notes' => 'Order from text: '.$request->order_text,
                    ]);

                    $order->items()->create([
                        'menu_item_id' => null,
                        'name' => $request->order_text,
                        'quantity' => 1,
                        'price' => 0,
                        'total' => 0,
                    ]);

                    // Log Activity
                    Activity::create([
                        'description' => "New WhatsApp text order #{$order->id} from {$request->customer_phone}: \"{$request->order_text}\" (Unmatched)",
                        'type' => 'order_created',
                        'properties' => [
                            'order_id' => $order->id,
                            'source' => 'whatsapp_text_unmatched',
                            'waiter_id' => $request->waiter_id,
                        ],
                    ]);

                    return response()->json([
                        'success' => true,
                        'order_id' => $order->id,
                        'total' => 0,
                        'items' => [['name' => $request->order_text, 'quantity' => 1, 'price' => 0]],
                        'message' => 'Booking created successfully. Stylist will confirm the price.',
                    ]);
                });
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first(),
                ], 422);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }

        // Create Order with matched items
        try {
            return DB::transaction(function () use ($request, $matchedItems, $totalAmount, $tableNumber) {
                $order = Order::withoutGlobalScopes()->create([
                    'restaurant_id' => $request->restaurant_id,
                    'waiter_id' => $request->waiter_id,
                    'table_number' => $tableNumber,
                    'customer_phone' => $request->customer_phone,
                    'customer_name' => $request->customer_name,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                ]);

                foreach ($matchedItems as $item) {
                    $order->items()->create([
                        'menu_item_id' => $item['menu_item_id'],
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['subtotal'],
                    ]);
                }

                // Log Activity
                Activity::create([
                    'description' => "New WhatsApp text order #{$order->id} from {$request->customer_phone}: \"{$request->order_text}\"",
                    'type' => 'order_created',
                    'properties' => [
                        'order_id' => $order->id,
                        'source' => 'whatsapp_text',
                        'waiter_id' => $request->waiter_id,
                    ],
                ]);

                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'total' => $totalAmount,
                    'items' => $matchedItems,
                    'waiter_id' => $request->waiter_id,
                    'table_number' => $tableNumber,
                    'message' => 'Booking created successfully',
                ]);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
