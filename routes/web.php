<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Pos\AccountingController;
use App\Http\Controllers\Pos\ApiController;
use App\Http\Controllers\Pos\CashierController;
use App\Http\Controllers\Pos\CategoryController;
use App\Http\Controllers\Pos\CustomerController;
use App\Http\Controllers\Pos\DashboardController;
use App\Http\Controllers\Pos\InventoryController;
use App\Http\Controllers\Pos\ProductController;
use App\Http\Controllers\Pos\ReportController;
use App\Http\Controllers\Pos\ShiftController;
use Illuminate\Support\Facades\Route;

// Demo route (public, no auth required, allows iframe embedding)
Route::get('/demo', [\App\Http\Controllers\Pos\DemoController::class, 'index'])
    ->middleware('allow.iframe')
    ->name('demo.cashier');

// Auth routes
Route::get('/login', [LoginController::class, 'show'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Root redirect
Route::get('/', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('pos.dashboard');
    }
    return redirect()->route('login');
});

// Authenticated POS app
Route::middleware('auth')->name('pos.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Premium features - require active subscription
    Route::middleware('subscription')->group(function () {
        // Shifts
        Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::post('/shifts/open', [ShiftController::class, 'open'])->name('shifts.open');
        Route::post('/shifts/close', [ShiftController::class, 'close'])->name('shifts.close');

        // Cashier
        Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
        Route::middleware('shift.open')->group(function () {
            Route::post('/cashier/checkout', [CashierController::class, 'checkout'])->name('cashier.checkout');
            Route::get('/cashier/receipt/{transaction}', [CashierController::class, 'receipt'])->name('cashier.receipt');
            Route::post('/cashier/void/{transaction}', [CashierController::class, 'void'])->name('cashier.void');
        });

        // Inventory
        Route::resource('inventory', InventoryController::class);
        Route::post('/inventory/{inventory}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');

        // Products
        Route::resource('products', ProductController::class);
        Route::get('/products/{product}/barcode', [ProductController::class, 'generateBarcode'])->name('products.barcode');

        // Categories
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Customers
        Route::resource('customers', CustomerController::class);

        // Accounting
        Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
        Route::get('/accounting/cash-drawer', [AccountingController::class, 'cashDrawer'])->name('accounting.cash-drawer');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/products', [ReportController::class, 'products'])->name('products');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        });

        // API endpoints for search/autocomplete
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/products/search', [ApiController::class, 'searchProducts'])->name('products.search');
            Route::get('/customers/search', [ApiController::class, 'searchCustomers'])->name('customers.search');
        });
    });
});


// Demo route (public, no auth required, allows iframe embedding)
Route::get('/demo', [\App\Http\Controllers\Pos\DemoController::class, 'index'])
    ->middleware('allow.iframe')
    ->name('demo.cashier');

// Dev-only: Quick login for testing
Route::get('/dev/login/{email}', \App\Http\Controllers\Auth\DevLoginController::class)
    ->name('dev.login');

// Debug route to check session
Route::get('/debug/session', function (\Illuminate\Http\Request $request) {
    $guardName = \Illuminate\Support\Facades\Auth::getDefaultDriver();
    $guardClass = get_class(\Illuminate\Support\Facades\Auth::guard($guardName));
    $sessionKey = 'login_' . $guardName . '_' . sha1($guardClass);
    
    return response()->json([
        'authenticated' => \Illuminate\Support\Facades\Auth::check(),
        'user' => \Illuminate\Support\Facades\Auth::user(),
        'session_id' => session()->getId(),
        'session_key_looking_for' => $sessionKey,
        'session_data' => $request->session()->all(),
        'cookie_name' => config('session.cookie'),
    ]);
})->name('debug.session');

// SSO callback route
Route::get('/sso/callback', [SsoCallbackController::class, 'callback'])->name('sso.callback');

// Unauthenticated users are redirected to the main website login.
// There is no login page on this subdomain — auth is handled by balitechsolution.com.
Route::get('/', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('pos.dashboard');
    }
    return redirect(config('app.main_url', 'https://balitechsolution.com') . '/login');
});

// Logout: clear local session then redirect back to main website
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect(config('app.main_url', 'https://balitechsolution.com'));
})->middleware('auth')->name('logout');

// Authenticated POS app (deployed at pos.balitechsolution.com root)
// Apply 'subscription' middleware to routes that require paid subscription
Route::middleware('auth')->name('pos.')->group(function () {
    Route::get('/', fn () => redirect()->route('pos.dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Free tier routes (no subscription required) - only view demo and basic info
    // All other routes below require subscription

    // Premium features - require active subscription
    Route::middleware('subscription')->group(function () {
        // Shifts
        Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::post('/shifts/open', [ShiftController::class, 'open'])->name('shifts.open');
        Route::post('/shifts/close', [ShiftController::class, 'close'])->name('shifts.close');

        // Cashier
        Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
        Route::middleware('shift.open')->group(function () {
            Route::post('/cashier/checkout', [CashierController::class, 'checkout'])->name('cashier.checkout');
            Route::get('/cashier/receipt/{transaction}', [CashierController::class, 'receipt'])->name('cashier.receipt');
            Route::post('/cashier/void/{transaction}', [CashierController::class, 'void'])->name('cashier.void');
        });

        // Inventory
        Route::resource('inventory', InventoryController::class);
        Route::post('/inventory/{inventory}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');

        // Products
        Route::resource('products', ProductController::class);
        Route::get('/products/{product}/barcode', [ProductController::class, 'generateBarcode'])->name('products.barcode');

        // Categories
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Customers
        Route::resource('customers', CustomerController::class);

        // Accounting
        Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
        Route::get('/accounting/cash-drawer', [AccountingController::class, 'cashDrawer'])->name('accounting.cash-drawer');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/products', [ReportController::class, 'products'])->name('products');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        });

        // API endpoints for search/autocomplete
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/products/search', [ApiController::class, 'searchProducts'])->name('products.search');
            Route::get('/customers/search', [ApiController::class, 'searchCustomers'])->name('customers.search');
        });
    });
});
