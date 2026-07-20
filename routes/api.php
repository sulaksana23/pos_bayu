<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\ExpenseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| POS API Routes
|--------------------------------------------------------------------------
|
| Token-based API authentication via Laravel Sanctum.
|
| Public endpoints:
|   POST /api/auth/login    — Login & get token
|
| Protected endpoints (Bearer token required):
|   POST   /api/auth/logout         — Revoke token
|   GET    /api/auth/user           — Current user info
|   GET    /api/products            — List products
|   GET    /api/customers           — List customers
|   GET    /api/categories          — List categories
|   GET    /api/transactions        — List transactions
|   GET    /api/reports/sales       — Sales report
|   GET    /api/reports/expenses    — Expense report
|   GET    /api/suppliers           — List suppliers
|   GET    /api/purchase-orders     — List purchase orders
|   GET    /api/expenses            — List expenses
|
*/

// ── Public ──
Route::post('/auth/login', [AuthController::class, 'login']);

// ── Protected (Sanctum) ──
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // Master data
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{customer}', [CustomerController::class, 'show']);

    Route::get('/categories', [CategoryController::class, 'index']);

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);

    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales']);
    Route::get('/reports/expenses', [ReportController::class, 'expenses']);

    // Professional features
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show']);

    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index']);
    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show']);

    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::get('/expenses/{expense}', [ExpenseController::class, 'show']);
});
