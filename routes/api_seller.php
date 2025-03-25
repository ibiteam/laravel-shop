<?php

use App\Http\Controllers\Seller\SellerEnterController;
use Illuminate\Support\Facades\Route;

Route::middleware([/*'auth:sanctum'*/])->group(function () {
    // 商家入驻
    Route::prefix('seller_enter')->group(function () {
        Route::get('check', [SellerEnterController::class, 'check']); // 入驻状态检测
        Route::get('configs', [SellerEnterController::class, 'enterConfigs']); // 入驻表单信息
        Route::post('store', [SellerEnterController::class, 'store']); // 提交入驻信息
        Route::post('shop/create', [SellerEnterController::class, 'shopCreate']); // 创建店铺信息
    });
});


