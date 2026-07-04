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
use App\Http\Controllers\Pos\UserController;
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

    // Users (admin only) — outside subscription check
    Route::middleware('role:admin')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::patch('/{user}/toggle', [UserController::class, 'toggleActive'])->name('toggle');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

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
