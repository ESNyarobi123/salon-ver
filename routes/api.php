<?php

/**
 * TAPTAP API Routes
 *
 * All API routes with their controller and method:
 *
 * AUTH (public)
 *   POST /auth/register-waiter          -> Api\WaiterRegistrationController@register
 *   POST /auth/login                    -> AuthController@login
 *   POST /auth/logout                    -> AuthController@logout (auth:sanctum)
 *
 * WAITER (prefix: /waiter, auth:sanctum + role:waiter)
 *   GET  /dashboard                     -> Api\Waiter\DashboardController@index
 *   GET  /dashboard/stats               -> Api\Waiter\DashboardController@stats
 *   GET  /orders                        -> Api\Waiter\DashboardController@orders
 *   GET  /tips                          -> Api\Waiter\DashboardController@tips
 *   GET  /ratings                       -> Api\Waiter\DashboardController@ratings
 *   GET  /menu                          -> Api\Waiter\MenuController@index
 *   GET  /requests                      -> Api\Waiter\DashboardController@pendingRequests
 *   GET  /payments                      -> Api\Waiter\DashboardController@payments
 *   GET  /my-tables                     -> Api\Waiter\DashboardController@myTables
 *   GET  /colleagues                    -> Api\Waiter\DashboardController@colleagues
 *   POST /handover-tables               -> Api\Waiter\DashboardController@handoverTables
 *   POST /orders/{order}/claim          -> Api\Waiter\DashboardController@claimOrder
 *   POST /requests/{customerRequest}/complete -> Api\Waiter\DashboardController@completeRequest
 *   GET  /salary-slips                  -> Api\Waiter\SalarySlipController@index
 *   GET  /salary-slips/{period}         -> Api\Waiter\SalarySlipController@show
 *   GET  /salary-slips/{period}/download -> Api\Waiter\SalarySlipController@download (PDF)
 *   GET  /history                       -> Api\Waiter\HistoryController@index (waiter's work history)
 *   PATCH /status                       -> Api\Waiter\DashboardController@updateStatus (body: is_online)
 *
 * V1 (prefix: /v1, auth:sanctum)
 *   GET  /restaurants/search            -> V1\RestaurantController@search
 *   GET  /restaurants/{restaurant}      -> V1\RestaurantController@show
 *   GET  /restaurants/{restaurant}/categories -> V1\MenuController@categories
 *   GET  /restaurants/{restaurant}/menu -> V1\MenuController@index
 *   POST /orders                        -> V1\OrderController@store
 *   GET  /orders/{order}                -> V1\OrderController@show
 *   GET  /orders/{order}/status         -> V1\OrderController@status
 *   PATCH/orders/{order}/status         -> V1\OrderController@updateStatus
 *   POST /payments/ussd-request         -> V1\PaymentController@ussdRequest
 *   POST /payments/cash/change-notification -> V1\PaymentController@cashChangeNotification
 *   POST /payments/cash                 -> V1\PaymentController@cashPayment
 *   GET  /payments/{order}/status       -> V1\PaymentController@status
 *   POST /feedback                      -> V1\FeedbackController@store
 *   POST /tips                         -> V1\TipController@store
 *
 * V1/MANAGER (prefix: /v1/manager, auth:sanctum + role:manager)
 *   apiResource categories, menu, tables -> Api\Manager\*Controller (index, store, show, update, destroy)
 *   GET  /waiters                        -> Api\Manager\WaiterController@index
 *   GET  /waiters/search?q=              -> Api\Manager\WaiterController@search (unique code e.g. TIPTAP-W-00001)
 *   GET  /waiters/history                -> Api\Manager\WaiterController@history
 *   POST /waiters/{waiter}/link          -> Api\Manager\WaiterController@link (body: employment_type, linked_until?)
 *   POST /waiters/{waiter}/unlink        -> Api\Manager\WaiterController@unlink
 *   GET  /payroll                        -> Api\Manager\PayrollController@index
 *   POST /payroll                        -> Api\Manager\PayrollController@store
 *   GET  /payroll/history                -> Api\Manager\PayrollController@history
 *   GET  /payroll/export                 -> Api\Manager\PayrollController@export
 *
 * BOT (prefix: /bot, auth:sanctum) -> WhatsAppBotController
 *   verifyRestaurant, verifyTag, parseEntry, searchRestaurant, getFullMenu, getCategories,
 *   getCategoryItems, getItemDetails, createOrder, createOrderByText, getOrderStatus,
 *   initiatePayment, submitFeedback, submitTip, getTables, waiterStatus, callWaiter, getWaiters,
 *   getActiveOrder, getMenuImage, initiateQuickPayment, getQuickPaymentStatus
 *
 * ORDER PORTAL (prefix: /order-portal, auth: session via password)
 *   POST /order-portal/login              -> OrderPortal\LoginController@store (body: password)
 *   POST /order-portal/logout             -> OrderPortal\LoginController@destroy (order.portal)
 *   GET  /order-portal/orders             -> OrderPortal\LiveOrdersController@index (order.portal)
 *   POST /order-portal/orders             -> OrderPortal\LiveOrdersController@store (order.portal)
 *   PUT  /order-portal/orders/{order}     -> OrderPortal\LiveOrdersController@update (order.portal)
 *   DELETE /order-portal/orders/{order}   -> OrderPortal\LiveOrdersController@destroy (order.portal)
 *   POST /order-portal/payments/selcom/initiate -> OrderPortal\LiveOrdersController@paymentInitiate (order.portal)
 *   GET  /order-portal/payments/selcom/status/{order} -> OrderPortal\LiveOrdersController@paymentStatus (order.portal)
 */

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\FeedbackController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\RestaurantController;
use App\Http\Controllers\Api\V1\TipController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth API (no middleware - public)
Route::post('/auth/register-waiter', [App\Http\Controllers\Api\WaiterRegistrationController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Waiter API (auth:sanctum + role:waiter)
Route::prefix('waiter')->middleware(['auth:sanctum', 'role:waiter'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Api\Waiter\DashboardController::class, 'index']);
    Route::get('/dashboard/stats', [\App\Http\Controllers\Api\Waiter\DashboardController::class, 'stats']);
    Route::get('/orders', [\App\Http\Controllers\Api\Waiter\DashboardController::class, 'orders']);
    Route::get('/tips', [\App\Http\Controllers\Api\Waiter\DashboardController::class, 'tips']);
    Route::get('/ratings', [\App\Http\Controllers\Api\Waiter\DashboardController::class, 'ratings']);
    Route::get('/menu', [\App\Http\Controllers\Api\Waiter\MenuController::class, 'index']);
    Route::get('/requests', [\App\Http\Controllers\Api\Waiter\DashboardController::class, 'pendingRequests']);
    Route::get('/payments', [\App\Http\Controllers\Api\Waiter\DashboardController::class, 'payments']);
    Route::get('/my-tables', [\App\Http\Controllers\Api\Waiter\DashboardController::class, 'myTables']);
    Route::get('/colleagues', [\App\Http\Controllers\Api\Waiter\DashboardController::class, 'colleagues']);
    Route::post('/handover-tables', [\App\Http\Controllers\Api\Waiter\DashboardController::class, 'handoverTables']);
    Route::post('/orders/{order}/claim', [\App\Http\Controllers\Api\Waiter\DashboardController::class, 'claimOrder']);
    Route::post('/requests/{customerRequest}/complete', [\App\Http\Controllers\Api\Waiter\DashboardController::class, 'completeRequest']);
    Route::get('/salary-slips', [\App\Http\Controllers\Api\Waiter\SalarySlipController::class, 'index']);
    Route::get('/salary-slips/{period}', [\App\Http\Controllers\Api\Waiter\SalarySlipController::class, 'show'])->where('period', '[0-9]{4}-[0-9]{2}');
    Route::get('/salary-slips/{period}/download', [\App\Http\Controllers\Api\Waiter\SalarySlipController::class, 'download'])->where('period', '[0-9]{4}-[0-9]{2}');
    Route::get('/history', [\App\Http\Controllers\Api\Waiter\HistoryController::class, 'index']);
    Route::patch('/status', [\App\Http\Controllers\Api\Waiter\DashboardController::class, 'updateStatus']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Restaurants
    Route::get('/restaurants/search', [RestaurantController::class, 'search']);
    Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show']);

    // Menu
    Route::get('/restaurants/{restaurant}/categories', [MenuController::class, 'categories']);
    Route::get('/restaurants/{restaurant}/menu', [MenuController::class, 'index']);

    // Orders
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::get('/orders/{order}/status', [OrderController::class, 'status']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);

    // Payments
    Route::post('/payments/ussd-request', [PaymentController::class, 'ussdRequest']);
    Route::post('/payments/cash/change-notification', [PaymentController::class, 'cashChangeNotification']);
    Route::post('/payments/cash', [PaymentController::class, 'cashPayment']);
    Route::get('/payments/{order}/status', [PaymentController::class, 'status']);

    // Feedback & Tips
    Route::post('/feedback', [FeedbackController::class, 'store']);
    Route::post('/tips', [TipController::class, 'store']);
});

// Manager API Routes
Route::prefix('v1/manager')->middleware(['auth:sanctum', 'role:manager'])->group(function () {
    // Categories
    Route::apiResource('categories', \App\Http\Controllers\Api\Manager\CategoryController::class);

    // Menu
    Route::apiResource('menu', \App\Http\Controllers\Api\Manager\MenuController::class);

    // Tables
    Route::apiResource('tables', \App\Http\Controllers\Api\Manager\TableController::class);

    // Waiters (link / unlink / search by unique code; waiter_code is generated on link)
    Route::get('/waiters', [\App\Http\Controllers\Api\Manager\WaiterController::class, 'index']);
    Route::get('/waiters/search', [\App\Http\Controllers\Api\Manager\WaiterController::class, 'search']);
    Route::get('/waiters/history', [\App\Http\Controllers\Api\Manager\WaiterController::class, 'history']);
    Route::post('/waiters/{waiter}/link', [\App\Http\Controllers\Api\Manager\WaiterController::class, 'link']);
    Route::post('/waiters/{waiter}/unlink', [\App\Http\Controllers\Api\Manager\WaiterController::class, 'unlink']);

    // Payroll
    Route::get('/payroll', [\App\Http\Controllers\Api\Manager\PayrollController::class, 'index']);
    Route::post('/payroll', [\App\Http\Controllers\Api\Manager\PayrollController::class, 'store']);
    Route::get('/payroll/history', [\App\Http\Controllers\Api\Manager\PayrollController::class, 'history']);
    Route::get('/payroll/export', [\App\Http\Controllers\Api\Manager\PayrollController::class, 'export']);
});

// WhatsApp Bot Routes
Route::prefix('bot')->middleware('auth:sanctum')->group(function () {
    Route::get('/verify-restaurant', [App\Http\Controllers\Api\WhatsAppBotController::class, 'verifyRestaurant']);
    Route::get('/verify-tag', [App\Http\Controllers\Api\WhatsAppBotController::class, 'verifyTag']);
    Route::match(['get', 'post'], '/parse-entry', [App\Http\Controllers\Api\WhatsAppBotController::class, 'parseEntry']);
    Route::get('/search-restaurant', [App\Http\Controllers\Api\WhatsAppBotController::class, 'searchRestaurant']);
    Route::get('/restaurant/{restaurantId}/full-menu', [App\Http\Controllers\Api\WhatsAppBotController::class, 'getFullMenu']);
    Route::get('/restaurant/{restaurantId}/categories', [App\Http\Controllers\Api\WhatsAppBotController::class, 'getCategories']);
    Route::get('/category/{categoryId}/items', [App\Http\Controllers\Api\WhatsAppBotController::class, 'getCategoryItems']);
    Route::get('/item/{itemId}', [App\Http\Controllers\Api\WhatsAppBotController::class, 'getItemDetails']);
    Route::post('/order', [App\Http\Controllers\Api\WhatsAppBotController::class, 'createOrder']);
    Route::post('/order/text', [App\Http\Controllers\Api\WhatsAppBotController::class, 'createOrderByText']);
    Route::get('/order/{orderId}/status', [App\Http\Controllers\Api\WhatsAppBotController::class, 'getOrderStatus']);
    Route::post('/payment/ussd', [App\Http\Controllers\Api\WhatsAppBotController::class, 'initiatePayment']);
    Route::post('/feedback', [App\Http\Controllers\Api\WhatsAppBotController::class, 'submitFeedback']);
    Route::post('/tip', [App\Http\Controllers\Api\WhatsAppBotController::class, 'submitTip']);
    Route::get('/restaurant/{restaurantId}/tables', [App\Http\Controllers\Api\WhatsAppBotController::class, 'getTables']);
    Route::get('/waiter/{waiterId}/status', [App\Http\Controllers\Api\WhatsAppBotController::class, 'waiterStatus']);
    Route::post('/call-waiter', [App\Http\Controllers\Api\WhatsAppBotController::class, 'callWaiter']);
    Route::get('/restaurant/{restaurantId}/waiters', [App\Http\Controllers\Api\WhatsAppBotController::class, 'getWaiters']);
    Route::get('/active-order', [App\Http\Controllers\Api\WhatsAppBotController::class, 'getActiveOrder']);
    Route::get('/restaurant/{restaurantId}/menu-image', [App\Http\Controllers\Api\WhatsAppBotController::class, 'getMenuImage']);
    Route::get('/restaurant/{restaurantId}/guest-wifi', [App\Http\Controllers\Api\WhatsAppBotController::class, 'getGuestWifi']);

    // Quick Payment Routes (payment without order)
    Route::post('/payment/quick', [App\Http\Controllers\Api\WhatsAppBotController::class, 'initiateQuickPayment']);
    Route::get('/payment/quick/{paymentId}/status', [App\Http\Controllers\Api\WhatsAppBotController::class, 'getQuickPaymentStatus']);
});

// WhatsApp Webhook (Meta/WhatsApp Cloud API)
Route::get('/whatsapp/webhook', [App\Http\Controllers\Api\WhatsAppWebhookController::class, 'verify']);
Route::post('/whatsapp/webhook', [App\Http\Controllers\Api\WhatsAppWebhookController::class, 'handle']);

// Order Portal API (session auth via password; use Accept: application/json + Cookie for JSON)
Route::prefix('order-portal')->middleware('web')->group(function () {
    Route::post('/login', [\App\Http\Controllers\OrderPortal\LoginController::class, 'store']);
    Route::post('/logout', [\App\Http\Controllers\OrderPortal\LoginController::class, 'destroy'])
        ->middleware('order.portal');

    Route::middleware('order.portal')->group(function () {
        Route::get('/orders', [\App\Http\Controllers\OrderPortal\LiveOrdersController::class, 'index']);
        Route::post('/orders', [\App\Http\Controllers\OrderPortal\LiveOrdersController::class, 'store']);
        Route::put('/orders/{order}', [\App\Http\Controllers\OrderPortal\LiveOrdersController::class, 'update']);
        Route::delete('/orders/{order}', [\App\Http\Controllers\OrderPortal\LiveOrdersController::class, 'destroy']);
        Route::post('/payments/selcom/initiate', [\App\Http\Controllers\OrderPortal\LiveOrdersController::class, 'paymentInitiate']);
        Route::get('/payments/selcom/status/{order}', [\App\Http\Controllers\OrderPortal\LiveOrdersController::class, 'paymentStatus']);
    });
});
