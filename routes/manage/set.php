<?php

use App\Http\Controllers\Manage\RouterCategoryController;
use App\Http\Controllers\Manage\RouterController;
use App\Http\Controllers\Manage\ShopConfigController;
use Illuminate\Support\Facades\Route;

Route::prefix('set')->group(function () {
    // 商店设置
    Route::prefix('shop_config')->group(function () {
        Route::get('', [ShopConfigController::class, 'index']);
        Route::post('update', [ShopConfigController::class, 'update']);
    });

    // 路由
    Route::prefix('router_category')->group(function () {
        Route::get('', [RouterCategoryController::class, 'index']);
        Route::post('store', [RouterCategoryController::class, 'store']);
        Route::post('change_show', [RouterCategoryController::class, 'changeShow']);
    });
    Route::prefix('router')->group(function () {
        Route::get('', [RouterController::class, 'index']);
        Route::get('categories', [RouterController::class, 'categories']);
        Route::post('store', [RouterController::class, 'store']);
        Route::post('change_show', [RouterController::class, 'changeShow']);
    });
});
