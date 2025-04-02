<?php

use App\Http\Controllers\Manage\AppDecorationController;
use App\Http\Controllers\Manage\PaymentMethodController;
use App\Http\Controllers\Manage\RouterCategoryController;
use App\Http\Controllers\Manage\RouterController;
use App\Http\Controllers\Manage\ShopConfigController;
use App\Http\Controllers\Manage\MaterialFileController;
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
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_ROUTER_CATEGORY_UPDATE])->group(function () {
            Route::post('store', [RouterCategoryController::class, 'store']);
            Route::post('change_show', [RouterCategoryController::class, 'changeShow']);
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
});

// 素材中心
Route::prefix('material')->group(function () {
    Route::middleware(['manage.permission:' . Permission::MANAGE_MATERIAL_CENTER])->group(function () {
        Route::get('/', [MaterialFileController::class, 'index'])->name(Permission::MANAGE_MATERIAL_CENTER); // 素材列表
        Route::group(['prefix' => 'folder'], function () {
            Route::get('/', [MaterialFileController::class, 'folderList']); // 文件夹列表
            Route::get('list', [MaterialFileController::class, 'folderListForDirType']); // 上级文件夹
        });
    });
    Route::middleware(['manage.permission:' . Permission::MANAGE_MATERIAL_CENTER_UPDATE])->group(function () {
        Route::post('/rename', [MaterialFileController::class, 'rename']); // 修改素材文件名
        Route::post('/new/folder', [MaterialFileController::class, 'newFolder']); // 新建文件夹
        Route::post('/move', [MaterialFileController::class, 'move']);// 移动
        Route::post('/batch/move', [MaterialFileController::class, 'batchMove']);// 批量移动
        Route::post('/upload', [MaterialFileController::class, 'upload']);// 上传素材
    });
    Route::middleware(['manage.permission:' . Permission::MANAGE_MATERIAL_CENTER_DELETE])->group(function () {
        Route::post('/destory', [MaterialFileController::class, 'destory']);// 删除
        Route::post('/batch/destory', [MaterialFileController::class, 'batchDestroy']);// 批量删除
    });
});

// 移动端装修
Route::prefix('app_decoration')->group(function () {
    Route::middleware(['manage.permission:' . Permission::MANAGE_APP_DECORATION])->group(function () {
        Route::get('/', [AppDecorationController::class, 'index'])->name(Permission::MANAGE_APP_DECORATION); // 移动端装修
    });
    Route::middleware(['manage.permission:' . Permission::MANAGE_MATERIAL_CENTER_UPDATE])->group(function () {

    });
    Route::middleware(['manage.permission:' . Permission::MANAGE_MATERIAL_CENTER_DELETE])->group(function () {

    });
});
