<?php

use App\Http\Controllers\Api\V1\AccountSetController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\SmsController;
use Illuminate\Support\Facades\Route;

/* 发送短信 */
Route::post('sms-action', [SmsController::class, 'handleAction']);
/* 授权相关路由 */
Route::prefix('auth')->group(function () {
    Route::get('check-phone', [AuthController::class, 'checkPhone']); // 检测手机号是否注册
    Route::get('check-login', [AuthController::class, 'checkLogin']); // 检测是否登录
    Route::post('register-by-phone', [AuthController::class, 'registerByPhone']); // 手机号注册
    Route::post('login-by-password', [AuthController::class, 'loginByPassword']); // 账号(用户名+手机号)密码登录
    Route::post('login-by-phone', [AuthController::class, 'loginByPhone']); // 手机号登录
    Route::post('login-register-by-phone', [AuthController::class, 'loginAndRegisterByPhone']); // 手机号登录或注册
    Route::post('forget-password', [AuthController::class, 'forgetPassword']); // 忘记密码
});

// 搜索
Route::prefix('search')->group(function () {
    Route::post('goods', [SearchController::class, 'searchGoods']); // 搜索商品
    Route::get('keywords', [SearchController::class, 'getKeywords']); // 获取搜索推荐关键字
});

// 分类
Route::get('category', [CategoryController::class, 'index']); // 树状结构

/**
 * 登录可以访问路由.
 */
Route::middleware('api.auth')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']); // 退出登录
        Route::post('edit-password', [AuthController::class, 'editPassword']); // 修改密码
    });

    Route::get('region', [AddressController::class, 'region']); // 地区数据
    Route::get('region_group', [AddressController::class, 'regionGroup']); // 地区分组数据

    // 用户地址
    Route::prefix('address')->group(function () {
        Route::post('list', [AddressController::class, 'index']); // 收货地址
        Route::post('search_address', [AddressController::class, 'search_address']); // 搜索地址
        Route::post('show', [AddressController::class, 'show']); // 获取一条收货地址
        Route::post('default', [AddressController::class, 'setDefault']); // 设置默认地址
        Route::post('update', [AddressController::class, 'update']); // 添加|编辑 收货地址
        Route::post('destroy', [AddressController::class, 'destroy']); // 删除一条收货地址
        Route::post('batch_destroy', [AddressController::class, 'batch_destroy']); // 批量删除
    });

    // 个人信息设置
    Route::prefix('account_set')->group(function () {
        Route::get('get_info', [AccountSetController::class, 'getUserInfo']); // 获取用户信息 - 账户设置页面信息
        Route::post('user_name', [AccountSetController::class, 'setUserName']); // 修改用户名
        Route::post('nickname', [AccountSetController::class, 'setNickname']); // 修改昵称
        Route::post('avatar', [AccountSetController::class, 'setUserAvatar']); // 修改头像
        Route::post('phone', [AccountSetController::class, 'setUserPhone']); // 修改注册手机号
    });

    // 购物车
    Route::prefix('cart')->group(function () {
        Route::get('list', [CartController::class, 'list']);        // 商品列表
        Route::get('number', [CartController::class, 'number']);    // 有效购物车数量
        Route::post('store', [CartController::class, 'store']);     // 添加
        Route::post('destroy', [CartController::class, 'destroy']); // 删除
        Route::post('change_number', [CartController::class, 'changeNumber']);  // 变更数量
        Route::post('change_check', [CartController::class, 'changeCheck']);    // 变更选中结算
    });
});
