<?php

use App\Http\Controllers\Home\AuthController;
use App\Http\Controllers\Home\SmsController;
use Illuminate\Support\Facades\Route;

/* 发送短信 */
Route::post('sms-action', [SmsController::class, 'handleAction']);
/* 授权相关路由 */
Route::prefix('auth')->group(function () {
    Route::get('check-login', [AuthController::class, 'checkLogin']); // 检测是否登录
    Route::get('check-name', [AuthController::class, 'checkUserName']); // 检测用户名是否注册
    Route::get('check-phone', [AuthController::class, 'checkPhone']); // 检测手机号是否注册
    Route::post('register', [AuthController::class, 'register']); // 用户注册
    Route::post('login-by-password', [AuthController::class, 'loginByPassword']); // 账号(用户名+手机号)密码登录
    Route::post('login-by-phone', [AuthController::class, 'loginByPhone']); // 手机号验证码登录
    Route::post('forget-password', [AuthController::class, 'forgetPassword']); // 忘记密码
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']); // 退出登录
    Route::post('edit-password', [AuthController::class, 'editPassword']); // 修改密码
});
