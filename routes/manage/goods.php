<?php

use App\Http\Controllers\Manage\GoodsCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('goods')->group(function () {
    Route::prefix('category')->group(function () {
        Route::get('/', [GoodsCategoryController::class, 'index']); // 商品分类列表
        Route::get('/edit', [GoodsCategoryController::class, 'edit']); // 商品分类编辑
        Route::post('/update', [GoodsCategoryController::class, 'update']); // 商品分类更新(新增)
        Route::post('/destroy', [GoodsCategoryController::class, 'destroy']); // 商品分类删除
    });
});
