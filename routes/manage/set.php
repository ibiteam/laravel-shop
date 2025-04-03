<?php

use App\Http\Controllers\Manage\AppDecorationController;
use App\Http\Controllers\Manage\PaymentMethodController;
use App\Http\Controllers\Manage\RouterCategoryController;
use App\Http\Controllers\Manage\RouterController;
use App\Http\Controllers\Manage\ShopConfigController;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

Route::prefix('set')->group(function () {
    // 商店设置
    Route::prefix('shop_config')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_SHOP_CONFIG_INDEX])->group(function () {
            Route::get('', [ShopConfigController::class, 'index'])->name(Permission::MANAGE_SHOP_CONFIG_INDEX);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_SHOP_CONFIG_UPDATE])->group(function () {
            Route::post('update', [ShopConfigController::class, 'update']);
        });
    });

    // 访问地址分类
    Route::prefix('router_category')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_ROUTER_CATEGORY_INDEX])->group(function () {
            Route::get('', [RouterCategoryController::class, 'index'])->name(Permission::MANAGE_ROUTER_CATEGORY_INDEX);
            Route::get('info', [RouterCategoryController::class, 'info']);
            Route::get('pages', [RouterCategoryController::class, 'getPages']);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_ROUTER_CATEGORY_UPDATE])->group(function () {
            Route::post('store', [RouterCategoryController::class, 'store']);
            Route::post('change_show', [RouterCategoryController::class, 'changeShow']);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_ROUTER_CATEGORY_DELETE])->group(function () {
            Route::post('/destroy', [RouterCategoryController::class, 'destroy']);
        });
    });

    // 访问地址
    Route::prefix('router')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_ROUTER_INDEX])->group(function () {
            Route::get('', [RouterController::class, 'index'])->name(Permission::MANAGE_ROUTER_INDEX);
            Route::get('categories', [RouterController::class, 'categories']);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_ROUTER_UPDATE])->group(function () {
            Route::post('store', [RouterController::class, 'store']);
            Route::post('change_show', [RouterController::class, 'changeShow']);
        });
    });
    // 支付方式
    Route::prefix('payment/method')->group(function () {
        Route::get('/', [PaymentMethodController::class, 'index'])->name(Permission::MANAGE_PAYMENT_METHOD_INDEX)->middleware('manage.permission');
        Route::middleware('manage.permission:'.Permission::MANAGE_PAYMENT_METHOD_UPDATE)->group(function () {
            Route::get('edit', [PaymentMethodController::class, 'edit']);
            Route::post('update', [PaymentMethodController::class, 'update']);
            Route::post('change/field', [PaymentMethodController::class, 'changeField']);
        });
    });

    // 移动端装修
    Route::prefix('app_decoration')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_APP_DECORATION])->group(function () {
            Route::get('/', [AppDecorationController::class, 'index'])->name(Permission::MANAGE_APP_DECORATION); // 移动端装修
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_MATERIAL_CENTER_UPDATE])->group(function () {});
        Route::middleware(['manage.permission:'.Permission::MANAGE_MATERIAL_CENTER_DELETE])->group(function () {});
    });
});
