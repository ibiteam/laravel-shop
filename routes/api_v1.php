<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\SmsController;
use Illuminate\Support\Facades\Route;

/**
 * 未登录可以访问路由.
 */
Route::middleware([])->group(function () {
    Route::post('sms-action', [SmsController::class, 'handleAction']);

    Route::prefix('auth')->group(function () {
        Route::post('check-phone', [AuthController::class, 'checkPhone']); // 检测手机号是否注册

        Route::post('check-login', [AuthController::class, 'checkLogin']); // 检测是否登录

        Route::post('register-by-phone', [RegisterController::class, 'registerByPhone']);

        Route::post('login-by-phone', [LoginController::class, 'loginByPhone']);
    });
});

/**
 * 登录可以访问路由.
 */
Route::middleware('auth:sanctum')->group(function () {
    //
});
