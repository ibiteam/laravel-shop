<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\SmsController;
use Illuminate\Support\Facades\Route;

/**
 * 未登录可以访问路由.
 */
Route::middleware([])->group(function () {
    Route::post('sms-action', [SmsController::class, 'handleAction']);

    Route::post('register-by-phone', [RegisterController::class, 'registerByPhone']);

    Route::post('login-by-phone', [LoginController::class, 'loginByPhone']);
});

/**
 * 登录可以访问路由.
 */
Route::middleware('auth:sanctum')->group(function () {
    //
});
