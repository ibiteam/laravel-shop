<?php

use App\Http\Controllers\Manage\Marketing\BonusController;
use App\Http\Controllers\Manage\Marketing\CouponController;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

Route::prefix('marketing')->group(function () {
    // 红包
    Route::prefix('bonus')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_BONUS_INDEX])->group(function () {
            Route::get('/', [BonusController::class, 'index'])->name(Permission::MANAGE_BONUS_INDEX);
        });
    });
    // 优惠券
    Route::prefix('coupon')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_COUPON_INDEX])->group(function () {
            Route::get('/', [CouponController::class, 'index'])->name(Permission::MANAGE_COUPON_INDEX);
        });
    });
});
