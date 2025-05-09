<?php

use App\Http\Controllers\Manage\ApplyRefundController;
use App\Http\Controllers\Manage\ApplyRefundReasonController;
use App\Http\Controllers\Manage\OrderController;
use App\Http\Controllers\Manage\OrderDeliveryController;
use App\Http\Controllers\Manage\OrderEvaluateController;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

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
        Route::get('/', [ApplyRefundController::class, 'index'])->name(Permission::MANAGE_APPLY_REFUND_INDEX); // 列表
        Route::get('detail', [ApplyRefundController::class, 'detail']); // 详情
    });
    Route::middleware(['manage.permission:'.Permission::MANAGE_APPLY_REFUND_UPDATE])->group(function () {
        Route::post('agree_apply', [ApplyRefundController::class, 'agreeApply']);   // 同意申请
        Route::post('close_apply', [ApplyRefundController::class, 'closeApply']);   // 关闭申请
        Route::post('execute_refund', [ApplyRefundController::class, 'executeRefund']); // 执行退款
        Route::post('refuse_refund', [ApplyRefundController::class, 'refuseRefund']);   // 拒绝退款
        Route::post('confirm_receipt', [ApplyRefundController::class, 'confirmReceipt']);  // 确认收货
        Route::post('query_express', [ApplyRefundController::class, 'queryExpress']);   // 查询快递信息
    });
});

Route::prefix('order')->group(function () {
    // 订单列表
    Route::prefix('info')->group(function () {
        Route::get('/index', [OrderController::class, 'index'])->name(Permission::MANAGE_ORDER_INDEX)->middleware('manage.permission');
        Route::get('/detail', [OrderController::class, 'detail']);
        Route::get('ship/edit', [OrderController::class, 'shipEdit']);
        Route::post('ship/update', [OrderController::class, 'shipUpdate']);
        Route::get('address/edit', [OrderController::class, 'addressEdit']);
        Route::post('address/update', [OrderController::class, 'addressUpdate']);
        Route::get('express/query', [OrderController::class, 'queryExpress']);
    });

    // 发货管理
    Route::prefix('delivery')->group(function () {
        Route::middleware('manage.permission:'.Permission::MANAGE_ORDER_DELIVERY_INDEX)->group(function () {
            Route::get('/', [OrderDeliveryController::class, 'index'])->name(Permission::MANAGE_ORDER_DELIVERY_INDEX);
            Route::get('express/query', [OrderDeliveryController::class, 'queryExpress']);
        });
        Route::middleware('manage.permission:'.Permission::MANAGE_ORDER_DELIVERY_UPDATE)->group(function () {
            Route::post('import', [OrderDeliveryController::class, 'import']);
            Route::post('destroy', [OrderDeliveryController::class, 'destroy']);
        });
    });
    // 订单评价
    Route::prefix('evaluate')->group(function () {
        Route::middleware('manage.permission:'.Permission::MANAGE_ORDER_EVALUATE_INDEX)->group(function () {
            Route::get('/', [OrderEvaluateController::class, 'index'])->name(Permission::MANAGE_ORDER_EVALUATE_INDEX);
            Route::get('detail', [OrderEvaluateController::class, 'detail']);
        });
        Route::middleware('manage.permission:'.Permission::MANAGE_ORDER_EVALUATE_UPDATE)->group(function () {
            Route::post('check', [OrderEvaluateController::class, 'check']);
        });
    });
});
