<?php

use App\Http\Controllers\Manage\ApplyRefundController;
use App\Http\Controllers\Manage\ApplyRefundReasonController;
use App\Http\Controllers\Manage\OrderController;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

Route::prefix('order')->group(function () {
    // 订单列表
    Route::prefix('info')->group(function () {
        Route::get('/index', [OrderController::class, 'index'])->name(Permission::MANAGE_ORDER_INDEX)->middleware('manage.permission');
        Route::get('/detail', [OrderController::class, 'detail']);
        Route::get('ship/edit', [OrderController::class, 'shipEdit']);
        Route::post('ship/update', [OrderController::class, 'shipUpdate']);
        Route::get('address/edit', [OrderController::class, 'addressEdit']);
        Route::post('address/update', [OrderController::class, 'addressUpdate']);
    });

    // 退款原因
    Route::prefix('apply_refund_reason')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_APPLY_REFUND_REASON_INDEX])->group(function () {
            Route::get('/', [ApplyRefundReasonController::class, 'index'])->name(Permission::MANAGE_APPLY_REFUND_REASON_INDEX);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_APPLY_REFUND_REASON_UPDATE])->group(function () {
            Route::post('store', [ApplyRefundReasonController::class, 'store']);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_APPLY_REFUND_REASON_DELETE])->group(function () {
            Route::post('destroy', [ApplyRefundReasonController::class, 'destroy']);
        });
    });

    // 退款申请
    Route::prefix('apply_refund')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_APPLY_REFUND_INDEX])->group(function () {
            Route::get('/', [ApplyRefundController::class, 'index'])->name(Permission::MANAGE_APPLY_REFUND_INDEX);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_APPLY_REFUND_UPDATE])->group(function () {});
    });
});
