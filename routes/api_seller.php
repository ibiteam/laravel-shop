<?php

use App\Http\Controllers\Seller\SellerEnterController;
use Illuminate\Support\Facades\Route;

Route::middleware([/*'auth:sanctum'*/])->group(function () {
    // 商家入驻
    Route::prefix('seller_enter')->group(function () {
        Route::post('check', [SellerEnterController::class, 'check']); // 检测入驻状态
        Route::get('configs', [SellerEnterController::class, 'enterConfigs']); // 入驻表单信息
        Route::post('store', [SellerEnterController::class, 'store']); // 提交入驻信息
        Route::post('shop/create', [SellerEnterController::class, 'shopCreate']); // 更新店铺信息
    });
});


