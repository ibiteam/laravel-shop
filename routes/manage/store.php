<?php

use App\Http\Controllers\Manage\SellerEnterConfigController;
use App\Http\Controllers\Manage\SellerEnterController;
use Illuminate\Support\Facades\Route;

// 商家入驻配置
Route::prefix('seller_enter_config')->group(function () {
    Route::get('/', [SellerEnterConfigController::class, 'index'])->name('manage.seller_enter_config.index');
    Route::any('store', [SellerEnterConfigController::class, 'store'])->name('manage.seller_enter_config.store');
});

// 商家入驻列表
Route::prefix('seller_enter')->group(function () {
    Route::get('/', [SellerEnterController::class, 'index'])->name('manage.seller_enter.index');
});
