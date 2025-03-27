<?php

use App\Http\Controllers\Manage\GoodsCategoryController;
use App\Http\Controllers\Manage\GoodsController;
use Illuminate\Support\Facades\Route;

/* 商品管理 */
Route::prefix('goods')->group(function () {
    Route::get('/', [GoodsController::class, 'index'])->name('manage.goods.index');
    Route::get('/create', [GoodsController::class, 'create'])->name('manage.goods.create');

    Route::prefix('category')->group(function () {
        Route::get('/', [GoodsCategoryController::class, 'index'])->name('manage.goods.category');
    });
});
