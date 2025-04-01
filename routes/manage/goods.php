<?php

use App\Http\Controllers\Manage\GoodsCategoryController;
use App\Http\Controllers\Manage\GoodsController;
use App\Http\Controllers\Manage\GoodsParameterTemplateController;
use App\Http\Controllers\Manage\GoodsSkuTemplateController;
use App\Models\Permission as PermissionModel;
use Illuminate\Support\Facades\Route;

Route::prefix('goods')->group(function () {
    // 商品管理
    Route::get('info', [GoodsController::class, 'index'])->name(PermissionModel::MANAGE_GOODS_INDEX)->middleware('manage.permission');
    Route::middleware(['manage.permission:'.PermissionModel::MANAGE_GOODS_UPDATE])->group(function () {
        Route::prefix('info')->group(function () {
            Route::post('change/status', [GoodsController::class, 'changeStatus']);
            Route::get('edit', [GoodsController::class, 'edit']);
            Route::post('update', [GoodsController::class, 'update']);
        });
        // 商品参数模板
        Route::prefix('parameter/template')->group(function () {
            Route::get('small/index', [GoodsParameterTemplateController::class, 'smallIndex']);
            Route::post('store', [GoodsParameterTemplateController::class, 'store']);
            Route::post('update', [GoodsParameterTemplateController::class, 'update']);
            Route::post('destroy', [GoodsParameterTemplateController::class, 'destroy']);
        });
        // 商品规格模板
        Route::prefix('sku/template')->group(function () {
            Route::get('small/index', [GoodsSkuTemplateController::class, 'smallIndex']);
            Route::post('store', [GoodsSkuTemplateController::class, 'store']);
            Route::post('update', [GoodsSkuTemplateController::class, 'update']);
            Route::post('destroy', [GoodsSkuTemplateController::class, 'destroy']);
        });
    });
    // 商品分类
    Route::prefix('category')->group(function () {
        Route::get('/', [GoodsCategoryController::class, 'index']); // 商品分类列表
        Route::get('/edit', [GoodsCategoryController::class, 'edit']); // 商品分类编辑
        Route::post('/update', [GoodsCategoryController::class, 'update']); // 商品分类更新(新增)
        Route::post('/destroy', [GoodsCategoryController::class, 'destroy']); // 商品分类删除
    });
});
