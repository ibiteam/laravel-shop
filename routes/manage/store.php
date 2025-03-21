<?php

use App\Http\Controllers\Manage\SellerEnterConfigController;
use App\Http\Controllers\Manage\SellerEnterController;
use Illuminate\Support\Facades\Route;

// 商家入驻配置
Route::prefix('seller_enter_config')->group(function () {
    Route::get('/', [SellerEnterConfigController::class, 'index'])->name('manage.seller_enter_config.index');
    Route::any('update', [SellerEnterConfigController::class, 'update'])->name('manage.seller_enter_config.update');
});

// 商家入驻列表
Route::prefix('seller_enter')->group(function () {
    Route::get('/', [SellerEnterController::class, 'index'])->name('manage.seller_enter.index');
    Route::post('update_field', [SellerEnterController::class, 'updateField'])->name('manage.seller_enter.update_field');
    Route::any('check_status', [SellerEnterController::class, 'checkStatus'])->name('manage.seller_enter.check_status');
});
