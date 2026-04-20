<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboard;
use App\Http\Controllers\RestaurantRegistrationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/fix-storage', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');

    return 'Storage link created!';
});

// Serve profile & menu_images from storage (works when storage:link missing on host)
Route::get('/serve-storage/{path}', \App\Http\Controllers\ServeStorageController::class)->where('path', '.*')->name('storage.serve');

// DEBUG: Test Selcom Authentication - DELETE AFTER TESTING!
Route::get('/test-selcom', function () {
    $credentials = [
        'vendor_id' => 'TILL60917564',
        'api_key' => 'MOBIAD-BAE4439D874CAFF7',
        'api_secret' => '8PE3412A-7J3F0K7F-2A254AF-0P636D54',
        'is_live' => true, // Change to false for sandbox
    ];

    $timestamp = gmdate('Y-m-d\TH:i:s\Z');

    $payload = [
        'vendor' => $credentials['vendor_id'],
        'order_id' => 'TEST-'.time(),
        'buyer_email' => 'test@test.com',
        'buyer_name' => 'Test User',
        'buyer_phone' => '255678165524',
        'amount' => 1000,
        'currency' => 'TZS',
        'buyer_remarks' => 'Test Payment',
        'merchant_remarks' => 'Test Payment',
        'no_of_items' => 1,
    ];

    // Build signed fields string
    $signedFieldsList = array_keys($payload);
    $signedFields = implode(',', $signedFieldsList);

    // Build string to sign
    $stringToSign = 'timestamp='.$timestamp;
    foreach ($signedFieldsList as $field) {
        $stringToSign .= '&'.$field.'='.$payload[$field];
    }

    // Compute digest
    $digest = base64_encode(hash_hmac('sha256', $stringToSign, $credentials['api_secret'], true));

    $headers = [
        'Content-Type' => 'application/json',
        'Authorization' => 'SELCOM '.base64_encode($credentials['api_key']),
        'Digest-Method' => 'HS256',
        'Digest' => $digest,
        'Timestamp' => $timestamp,
        'Signed-Fields' => $signedFields,
    ];

    $baseUrl = $credentials['is_live']
        ? 'https://apigw.selcommobile.com/v1'
        : 'https://apigwtest.selcommobile.com/v1';

    // Make request
    $response = \Illuminate\Support\Facades\Http::withHeaders($headers)
        ->post($baseUrl.'/checkout/create-order-minimal', $payload);

    return response()->json([
        'debug' => [
            'timestamp' => $timestamp,
            'signed_fields' => $signedFields,
            'string_to_sign' => $stringToSign,
            'digest' => $digest,
            'authorization' => $headers['Authorization'],
            'base_url' => $baseUrl,
            'payload' => $payload,
        ],
        'response' => $response->json(),
        'status_code' => $response->status(),
    ]);
});

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/register-restaurant', [RestaurantRegistrationController::class, 'create'])->name('restaurant.register');
Route::post('/register-restaurant', [RestaurantRegistrationController::class, 'store'])->name('restaurant.register.store');

Route::get('/register-waiter', [\App\Http\Controllers\WaiterRegistrationController::class, 'create'])->name('waiter.register');
Route::post('/register-waiter', [\App\Http\Controllers\WaiterRegistrationController::class, 'store'])->name('waiter.register.store');

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->hasRole('super_admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('manager')) {
        return redirect()->route('manager.dashboard');
    } elseif ($user->hasRole('waiter')) {
        return redirect()->route('waiter.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes removed as ProfileController is not implemented yet
/*
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
*/

// Admin Portal
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [AdminDashboard::class, 'getStats'])->name('dashboard.stats');

    // Restaurants
    Route::resource('restaurants', \App\Http\Controllers\Admin\RestaurantController::class);
    Route::post('restaurants/{restaurant}/toggle-status', [\App\Http\Controllers\Admin\RestaurantController::class, 'toggleStatus'])->name('restaurants.toggle-status');

    // Users
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    // Waiters (all waiters + unique codes + search like manager)
    Route::get('waiters', [\App\Http\Controllers\Admin\WaiterController::class, 'index'])->name('waiters.index');
    Route::get('waiters/search', [\App\Http\Controllers\Admin\WaiterController::class, 'search'])->name('waiters.search');

    // Orders
    Route::get('orders/export', [\App\Http\Controllers\Admin\OrderController::class, 'export'])->name('orders.export');
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);

    // Payments
    Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/export', [\App\Http\Controllers\Admin\PaymentController::class, 'export'])->name('payments.export');
    Route::get('payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');

    // Withdrawals
    Route::get('withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('withdrawals/{withdrawal}/approve', [\App\Http\Controllers\Admin\WithdrawalController::class, 'approve'])->name('withdrawals.approve');
    Route::post('withdrawals/{withdrawal}/reject', [\App\Http\Controllers\Admin\WithdrawalController::class, 'reject'])->name('withdrawals.reject');

    // Bots
    Route::get('bots', [\App\Http\Controllers\Admin\BotController::class, 'index'])->name('bots.index');
    Route::post('bots/update-endpoint', [\App\Http\Controllers\Admin\BotController::class, 'updateEndpoint'])->name('bots.update-endpoint');
    Route::post('bots/generate-token', [\App\Http\Controllers\Admin\BotController::class, 'generateToken'])->name('bots.generate-token');

    // Notifications
    Route::get('notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/send', [\App\Http\Controllers\Admin\NotificationController::class, 'send'])->name('notifications.send');

    // Settings
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings/update', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
});

// Manager Portal
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerDashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [ManagerDashboard::class, 'getStats'])->name('dashboard.stats');
    Route::get('/live-orders', [\App\Http\Controllers\Manager\LiveOrderController::class, 'index'])->name('orders.live');
    Route::get('/live-orders/calendar', [\App\Http\Controllers\Manager\LiveOrderController::class, 'calendar'])->name('orders.calendar');
    Route::get('/product-sales', [\App\Http\Controllers\Manager\ProductSalesController::class, 'index'])->name('product-sales.index');
    Route::get('/orders/completed', [\App\Http\Controllers\Manager\CompletedBookingsController::class, 'index'])->name('orders.completed');
    Route::get('/orders/history', [\App\Http\Controllers\Manager\OrderHistoryController::class, 'index'])->name('orders.history');
    Route::get('/orders/history/export', [\App\Http\Controllers\Manager\OrderHistoryController::class, 'export'])->name('orders.history.export');
    Route::get('/orders/{order}', [\App\Http\Controllers\Manager\OrderHistoryController::class, 'show'])->name('orders.show');
    Route::post('/orders', [\App\Http\Controllers\Manager\LiveOrderController::class, 'store'])->name('orders.store');
    Route::put('/orders/{order}', [\App\Http\Controllers\Manager\LiveOrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [\App\Http\Controllers\Manager\LiveOrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/menu', [\App\Http\Controllers\Manager\MenuController::class, 'index'])->name('menu.index');
    Route::post('/menu', [\App\Http\Controllers\Manager\MenuController::class, 'store'])->name('menu.store');
    Route::put('/menu/{menuItem}', [\App\Http\Controllers\Manager\MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{menuItem}', [\App\Http\Controllers\Manager\MenuController::class, 'destroy'])->name('menu.destroy');
    Route::get('/stock', [\App\Http\Controllers\Manager\StockController::class, 'index'])->name('stock.index');
    Route::put('/stock/{menuItem}', [\App\Http\Controllers\Manager\StockController::class, 'update'])->name('stock.update');

    // Categories
    Route::post('/categories', [\App\Http\Controllers\Manager\CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [\App\Http\Controllers\Manager\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [\App\Http\Controllers\Manager\CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('/waiters', [\App\Http\Controllers\Manager\WaiterController::class, 'index'])->name('waiters.index');
    Route::get('/waiters/history', [\App\Http\Controllers\Manager\WaiterController::class, 'history'])->name('waiters.history');
    Route::get('/waiters/search', [\App\Http\Controllers\Manager\WaiterController::class, 'search'])->name('waiters.search');
    Route::post('/waiters/{waiter}/link', [\App\Http\Controllers\Manager\WaiterController::class, 'link'])->name('waiters.link');
    Route::post('/waiters/{waiter}/unlink', [\App\Http\Controllers\Manager\WaiterController::class, 'unlink'])->name('waiters.unlink');
    Route::post('/waiters/{waiter}/generate-order-portal-password', [\App\Http\Controllers\Manager\WaiterController::class, 'generateOrderPortalPassword'])->name('waiters.generate-order-portal-password');
    Route::get('/payroll', [\App\Http\Controllers\Manager\PayrollController::class, 'index'])->name('payroll.index');
    Route::post('/payroll', [\App\Http\Controllers\Manager\PayrollController::class, 'store'])->name('payroll.store');
    Route::get('/payroll/history', [\App\Http\Controllers\Manager\PayrollController::class, 'history'])->name('payroll.history');
    Route::get('/payroll/export', [\App\Http\Controllers\Manager\PayrollController::class, 'export'])->name('payroll.export');
    Route::get('/payments', [\App\Http\Controllers\Manager\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/export', [\App\Http\Controllers\Manager\PaymentController::class, 'export'])->name('payments.export');
    Route::post('/payments/selcom/initiate', [\App\Http\Controllers\Manager\PaymentController::class, 'initiateSelcom'])->name('payments.selcom.initiate');
    Route::get('/payments/selcom/status/{order}', [\App\Http\Controllers\Manager\PaymentController::class, 'checkSelcomStatus'])->name('payments.selcom.status');
    Route::get('/feedback', [\App\Http\Controllers\Manager\FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/tips', [\App\Http\Controllers\Manager\TipController::class, 'index'])->name('tips.index');
    Route::get('/api', [\App\Http\Controllers\Manager\ApiController::class, 'index'])->name('api.index');
    Route::post('/api/selcom', [\App\Http\Controllers\Manager\ApiController::class, 'updateSelcomCredentials'])->name('api.selcom.update');
    Route::post('/api/selcom/test', [\App\Http\Controllers\Manager\ApiController::class, 'testSelcomConnection'])->name('api.selcom.test');
    Route::post('/api/support-phone', [\App\Http\Controllers\Manager\ApiController::class, 'updateSupportPhone'])->name('api.support-phone.update');
    Route::post('/api/guest-wifi', [\App\Http\Controllers\Manager\ApiController::class, 'updateGuestWifi'])->name('api.guest-wifi.update');

    // Menu Image Upload
    Route::get('/menu-image', [\App\Http\Controllers\Manager\MenuImageController::class, 'index'])->name('menu-image.index');
    Route::post('/menu-image', [\App\Http\Controllers\Manager\MenuImageController::class, 'store'])->name('menu-image.store');
    Route::delete('/menu-image', [\App\Http\Controllers\Manager\MenuImageController::class, 'destroy'])->name('menu-image.destroy');

    Route::resource('tables', \App\Http\Controllers\Manager\TableController::class);
    Route::get('/help', [\App\Http\Controllers\Manager\HelpController::class, 'index'])->name('help.index');

    Route::get('/reports/performance', [\App\Http\Controllers\Manager\ReportController::class, 'performance'])->name('reports.performance');
    Route::get('/reports/export-performance', [\App\Http\Controllers\Manager\ReportController::class, 'exportPerformance'])->name('reports.export-performance');

    // Kitchen / floor display token (KDS)
    Route::post('/kitchen/generate-token', [\App\Http\Controllers\KitchenController::class, 'generateToken'])->name('kitchen.generate');
    Route::post('/kitchen/revoke-token', [\App\Http\Controllers\KitchenController::class, 'revokeToken'])->name('kitchen.revoke');
});

// Waiter Portal (dashboard allowed when not linked; other routes require linked restaurant)
Route::middleware(['auth', 'role:waiter'])->prefix('waiter')->name('waiter.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Waiter\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/history', [\App\Http\Controllers\Waiter\HistoryController::class, 'index'])->name('history');
    Route::post('/profile', [\App\Http\Controllers\Waiter\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/salary-slip', [\App\Http\Controllers\Waiter\SalarySlipController::class, 'index'])->name('salary-slip.index');
    Route::get('/salary-slip/{period}', [\App\Http\Controllers\Waiter\SalarySlipController::class, 'show'])->name('salary-slip.show')->where('period', '[0-9]{4}-[0-9]{2}');
    Route::get('/salary-slip/{period}/download', [\App\Http\Controllers\Waiter\SalarySlipController::class, 'download'])->name('salary-slip.download')->where('period', '[0-9]{4}-[0-9]{2}');
    Route::get('/help', [\App\Http\Controllers\Waiter\HelpController::class, 'index'])->name('help.index');
    Route::middleware('waiter.linked')->group(function () {
        Route::get('/dashboard/stats', [\App\Http\Controllers\Waiter\DashboardController::class, 'getStats'])->name('dashboard.stats');
        Route::get('/menu', [\App\Http\Controllers\Waiter\MenuController::class, 'index'])->name('menu');
        Route::get('/orders', [\App\Http\Controllers\Waiter\DashboardController::class, 'orders'])->name('orders');
        Route::get('/tips', [\App\Http\Controllers\Waiter\DashboardController::class, 'tips'])->name('tips');
        Route::get('/ratings', [\App\Http\Controllers\Waiter\DashboardController::class, 'ratings'])->name('ratings');
        Route::post('/requests/{request}/complete', [\App\Http\Controllers\Waiter\DashboardController::class, 'completeRequest'])->name('requests.complete');
        Route::post('/orders/{order}/claim', [\App\Http\Controllers\Waiter\DashboardController::class, 'claimOrder'])->name('orders.claim');
        Route::get('/handover', [\App\Http\Controllers\Waiter\DashboardController::class, 'handover'])->name('handover');
        Route::post('/handover', [\App\Http\Controllers\Waiter\DashboardController::class, 'handoverSubmit'])->name('handover.submit');
        Route::post('/status', [\App\Http\Controllers\Waiter\DashboardController::class, 'updateStatus'])->name('status.update');
    });
});

// Kitchen Display System (KDS) - Secret URL Access
Route::prefix('kitchen')->name('kitchen.')->group(function () {
    // Public KDS display (accessed via secret token)
    Route::get('/display/{token}', [\App\Http\Controllers\KitchenController::class, 'display'])->name('display');

    // API endpoints for real-time updates (no auth, uses token)
    Route::get('/api/{token}/orders', [\App\Http\Controllers\KitchenController::class, 'getOrders'])->name('api.orders');
    Route::get('/api/{token}/history', [\App\Http\Controllers\KitchenController::class, 'getOrderHistory'])->name('api.history');
    Route::post('/api/{token}/order/status', [\App\Http\Controllers\KitchenController::class, 'updateStatus'])->name('api.order.status');
    Route::post('/api/{token}/item/status', [\App\Http\Controllers\KitchenController::class, 'updateItemStatus'])->name('api.item.status');
});

// TIPTAP ORDER Portal (waiter login with manager-generated password)
Route::prefix('order-portal')->name('order-portal.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\OrderPortal\LoginController::class, 'create'])->name('login');
    Route::post('/login', [\App\Http\Controllers\OrderPortal\LoginController::class, 'store']);
    Route::post('/logout', [\App\Http\Controllers\OrderPortal\LoginController::class, 'destroy'])->name('logout');

    Route::middleware('order.portal')->group(function () {
        Route::get('/orders', [\App\Http\Controllers\OrderPortal\LiveOrdersController::class, 'index'])->name('orders');
        Route::post('/orders', [\App\Http\Controllers\OrderPortal\LiveOrdersController::class, 'store'])->name('orders.store');
        Route::put('/orders/{order}', [\App\Http\Controllers\OrderPortal\LiveOrdersController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [\App\Http\Controllers\OrderPortal\LiveOrdersController::class, 'destroy'])->name('orders.destroy');
        Route::post('/payments/selcom/initiate', [\App\Http\Controllers\OrderPortal\LiveOrdersController::class, 'paymentInitiate'])->name('payments.selcom.initiate');
        Route::get('/payments/selcom/status/{order}', [\App\Http\Controllers\OrderPortal\LiveOrdersController::class, 'paymentStatus'])->name('payments.selcom.status');
    });
});
