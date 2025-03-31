<?php

use App\Http\Controllers\Manage\ShopConfigController;
use Illuminate\Support\Facades\Route;

Route::prefix('set')->group(function () {
    // 商店设置
    Route::prefix('shop_config')->group(function () {
        Route::get('', [ShopConfigController::class, 'index']);
        Route::post('update', [ShopConfigController::class, 'update']);
    });
});
