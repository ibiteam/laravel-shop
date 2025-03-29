<?php

use App\Http\Controllers\Manage\UserController;
use App\Http\Controllers\Manage\UserAddressController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    // 用户列表
    Route::get('/index', [UserController::class, 'index']); // 用户列表
    Route::post('/update', [UserController::class, 'update']); // 新增用户
    // 用户地址
    Route::prefix('address')->group(function () {
        Route::get('/', [UserAddressController::class, 'index']); // 用户地址
        Route::post('/update', [UserAddressController::class, 'update']); // 用户地址更新
    });
});
