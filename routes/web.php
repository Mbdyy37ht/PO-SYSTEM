<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PurchaseOrderApprovalController;
use App\Http\Controllers\Admin\SalesOrderApprovalController;
use App\Http\Controllers\Admin\GoodReceiptNoteApprovalController;
use App\Http\Controllers\Admin\DeliveryApprovalController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\PurchaseOrderController as StaffPurchaseOrderController;
use App\Http\Controllers\Staff\SalesOrderController as StaffSalesOrderController;
use App\Http\Controllers\Staff\GoodReceiptNoteController as StaffGoodReceiptNoteController;
use App\Http\Controllers\Staff\DeliveryController as StaffDeliveryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'roleRedirect'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin/Manager Routes - Master Data
    Route::prefix('admin')->name('admin.')->middleware('isAdmin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('items', ItemController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('warehouses', WarehouseController::class);
        Route::resource('customers', CustomerController::class);

        // Approval Routes
        Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
            Route::get('approval', [PurchaseOrderApprovalController::class, 'index'])->name('approval');
            Route::get('{purchaseOrder}/show', [PurchaseOrderApprovalController::class, 'show'])->name('show');
            Route::post('{purchaseOrder}/approve', [PurchaseOrderApprovalController::class, 'approve'])->name('approve');
            Route::post('{purchaseOrder}/reject', [PurchaseOrderApprovalController::class, 'reject'])->name('reject');
        });

        Route::prefix('sales-orders')->name('sales-orders.')->group(function () {
            Route::get('approval', [SalesOrderApprovalController::class, 'index'])->name('approval');
            Route::get('{salesOrder}/show', [SalesOrderApprovalController::class, 'show'])->name('show');
            Route::post('{salesOrder}/approve', [SalesOrderApprovalController::class, 'approve'])->name('approve');
            Route::post('{salesOrder}/reject', [SalesOrderApprovalController::class, 'reject'])->name('reject');
        });

        Route::prefix('good-receipt-notes')->name('good-receipt-notes.')->group(function () {
            Route::get('approval', [GoodReceiptNoteApprovalController::class, 'index'])->name('approval');
            Route::get('{goodReceiptNote}/show', [GoodReceiptNoteApprovalController::class, 'show'])->name('show');
            Route::post('{goodReceiptNote}/approve', [GoodReceiptNoteApprovalController::class, 'approve'])->name('approve');
            Route::post('{goodReceiptNote}/reject', [GoodReceiptNoteApprovalController::class, 'reject'])->name('reject');
        });

        Route::prefix('deliveries')->name('deliveries.')->group(function () {
            Route::get('approval', [DeliveryApprovalController::class, 'index'])->name('approval');
            Route::get('{delivery}/show', [DeliveryApprovalController::class, 'show'])->name('show');
            Route::post('{delivery}/approve', [DeliveryApprovalController::class, 'approve'])->name('approve');
            Route::post('{delivery}/reject', [DeliveryApprovalController::class, 'reject'])->name('reject');
        });
    });

    // Staff Routes - Transactions
    Route::prefix('staff')->name('staff.')->middleware('isStaff')->group(function () {
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('purchase-orders', StaffPurchaseOrderController::class);
        
        Route::resource('sales-orders', StaffSalesOrderController::class);
        
        Route::resource('good-receipt-notes', StaffGoodReceiptNoteController::class);
        
        Route::resource('deliveries', StaffDeliveryController::class);
    });
});

require __DIR__.'/auth.php';
