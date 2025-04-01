<?php

use App\Http\Controllers\Manage\RouterCategoryController;
use App\Http\Controllers\Manage\RouterController;
use App\Http\Controllers\Manage\ShopConfigController;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

Route::prefix('set')->group(function () {
    // 商店设置
    Route::prefix('shop_config')->group(function () {
        Route::get('', [ShopConfigController::class, 'index'])->name(Permission::MANAGE_SHOP_CONFIG_INDEX);
        Route::post('update', [ShopConfigController::class, 'update']);
    });

    // 访问地址分类
    Route::prefix('router_category')->group(function () {
        Route::get('', [RouterCategoryController::class, 'index'])->name(Permission::MANAGE_ROUTER_CATEGORY_INDEX);
        Route::post('store', [RouterCategoryController::class, 'store']);
        Route::post('change_show', [RouterCategoryController::class, 'changeShow']);
    });

    // 访问地址
    Route::prefix('router')->group(function () {
        Route::get('', [RouterController::class, 'index'])->name(Permission::MANAGE_ROUTER_INDEX);
        Route::get('categories', [RouterController::class, 'categories']);
        Route::post('store', [RouterController::class, 'store']);
        Route::post('change_show', [RouterController::class, 'changeShow']);
    });
});
