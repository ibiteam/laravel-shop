<?php

use App\Http\Controllers\Api\V1\AuthController;
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

        Route::post('register-by-phone', [AuthController::class, 'registerByPhone']); // 手机号注册

        Route::post('login-by-password', [AuthController::class, 'loginByPassword']); // 账号(用户名+手机号)密码登录

        Route::post('login-by-phone', [AuthController::class, 'loginByPhone']); // 手机号登录

        Route::post('login-register-by-phone', [AuthController::class, 'loginAndRegisterByPhone']); // 手机号登录或注册
    });
});

/**
 * 登录可以访问路由.
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']); // 退出登录
    });
});
