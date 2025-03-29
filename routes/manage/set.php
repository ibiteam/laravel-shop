<?php

use App\Http\Controllers\Manage\ShopConfigController;
use Illuminate\Support\Facades\Route;

Route::prefix('set')->group(function () {
    // 商店设置
    Route::prefix('shop_config')->group(function () {
        Route::get('site_info', [ShopConfigController::class, 'siteInfo']); // 站点信息
        Route::get('site_logo', [ShopConfigController::class, 'siteLogo']); // 站点Logo
        Route::post('update', [ShopConfigController::class, 'update']); // 更新配置
    });
});
