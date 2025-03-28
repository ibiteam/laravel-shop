<?php

use App\Http\Controllers\Manage\GoodsBrandController;
use App\Http\Controllers\Manage\GoodsCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('goods')->group(function () {
    // 商品分类
    Route::prefix('category')->group(function () {
        Route::get('/', [GoodsCategoryController::class, 'index']); // 商品分类列表
        Route::get('/edit', [GoodsCategoryController::class, 'edit']); // 商品分类编辑
        Route::post('/update', [GoodsCategoryController::class, 'update']); // 商品分类更新(新增)
        Route::post('/destroy', [GoodsCategoryController::class, 'destroy']); // 商品分类删除
    });
    // 商品品牌
    Route::prefix('brand')->group(function () {
        Route::get('/', [GoodsBrandController::class, 'index']); // 商品品牌列表
        Route::get('/edit', [GoodsBrandController::class, 'edit']); // 商品品牌编辑
        Route::post('/update', [GoodsBrandController::class, 'update']); // 商品品牌更新(新增)
        Route::post('/destroy', [GoodsBrandController::class, 'destroy']); // 商品品牌删除
    });
});
