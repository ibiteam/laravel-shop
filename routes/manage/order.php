<?php

use App\Http\Controllers\Manage\OrderController;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

Route::prefix('order')->group(function () {
    // 订单列表相关
    Route::prefix('info')->group(function () {
        Route::get('/index', [OrderController::class, 'index'])->name(Permission::MANAGE_ORDER_INDEX)->middleware('manage.permission');
        Route::get('/detail', [OrderController::class, 'detail']);
        Route::get('ship/edit', [OrderController::class, 'shipEdit']);
        Route::post('ship/update', [OrderController::class, 'shipUpdate']);
        Route::get('address/edit', [OrderController::class, 'addressEdit']);
        Route::post('address/update', [OrderController::class, 'addressUpdate']);
    });
});
