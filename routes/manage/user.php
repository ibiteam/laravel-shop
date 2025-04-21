<?php

use App\Http\Controllers\Manage\UserAddressController;
use App\Http\Controllers\Manage\UserController;
use App\Http\Controllers\Manage\UserIntegralController;
use App\Http\Controllers\Manage\WechatUserController;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::middleware(['manage.permission:'.Permission::MANAGE_USER_INDEX])->group(function () {
        Route::get('/index', [UserController::class, 'index'])->name(Permission::MANAGE_USER_INDEX); // 会员列表
    });
    Route::middleware(['manage.permission:'.Permission::MANAGE_USER_UPDATE])->group(function () {
        Route::post('/update', [UserController::class, 'update']); // 新增用户
        // 用户地址
        Route::prefix('address')->group(function () {
            Route::get('/', [UserAddressController::class, 'index']); // 用户地址
            Route::post('/update', [UserAddressController::class, 'update']); // 用户地址更新
        });
    });
    Route::prefix('wechat')->group(function () {
        Route::get('/', [WechatUserController::class, 'index'])->name(Permission::MANAGE_WECHAT_USER_INDEX)->middleware('manage.permission');
    });
});
// 积分管理
Route::middleware(['manage.permission:'.Permission::MANAGE_USER_INTEGRAL_INDEX])->prefix('integral')->group(function () {
    // 用户积分
    Route::get('/', [UserIntegralController::class, 'index'])->name(Permission::MANAGE_USER_INTEGRAL_INDEX);
    // 积分明细
    Route::get('/detail', [UserIntegralController::class, 'detail'])->name(Permission::MANAGE_INTEGRAL_DETAIL_INDEX);
});
