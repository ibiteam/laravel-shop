<?php

use App\Http\Controllers\Manage\GoodsCategoryController;
use App\Http\Controllers\Manage\GoodsController;
use Illuminate\Support\Facades\Route;

Route::prefix('goods')->group(function () {
    // 商品管理
    Route::prefix('info')->group(function () {
        Route::get('/', [GoodsController::class, 'index']);
        Route::post('change-status', [GoodsController::class, 'changeStatus']);
        Route::get('edit', [GoodsController::class, 'edit']);
    });
    // 商品分类
    Route::prefix('category')->group(function () {
        Route::get('/', [GoodsCategoryController::class, 'index']); // 商品分类列表
        Route::get('/edit', [GoodsCategoryController::class, 'edit']); // 商品分类编辑
        Route::post('/update', [GoodsCategoryController::class, 'update']); // 商品分类更新(新增)
        Route::post('/destroy', [GoodsCategoryController::class, 'destroy']); // 商品分类删除
    });
});
