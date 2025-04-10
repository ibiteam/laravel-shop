<?php

use App\Http\Controllers\Api\V1\AccountSetController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CommonController;
use App\Http\Controllers\Api\V1\GoodsCollectController;
use App\Http\Controllers\Api\V1\GoodsController;
use App\Http\Controllers\Api\V1\Order\ApplyRefundController;
use App\Http\Controllers\Api\V1\Order\DoneController;
use App\Http\Controllers\Api\V1\Order\EvaluateController;
use App\Http\Controllers\Api\V1\Order\IndexController as MyOrderIndexController;
use App\Http\Controllers\Api\V1\Order\PayController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\SmsController;
use App\Http\Controllers\Api\V1\UploadController;
use Illuminate\Support\Facades\Route;

/* 发送短信 */
Route::post('sms-action', [SmsController::class, 'handleAction']);
Route::get('shop/config', [CommonController::class, 'shopConfig']);
Route::post('check/action/code', [SmsController::class, 'checkActionCode']);
// 文件上传
Route::post('upload', [UploadController::class, 'upload']);

/* 授权相关路由 */
Route::prefix('auth')->group(function () {
    Route::get('check_login', [AuthController::class, 'checkLogin']); // 检测是否登录
    Route::post('login/password', [AuthController::class, 'loginByPassword']); // 账号(用户名+手机号)密码登录
    Route::post('login/phone', [AuthController::class, 'loginByPhone']); // 手机号登录
    Route::post('forget/password', [AuthController::class, 'forgetPassword']); // 忘记密码
});

// 搜索
Route::prefix('search')->group(function () {
    Route::post('goods', [SearchController::class, 'searchGoods']); // 搜索商品
    Route::get('keywords', [SearchController::class, 'getKeywords']); // 获取搜索推荐关键字
});

// 分类
Route::get('category', [CategoryController::class, 'index']); // 树状结构

// 地区数据
Route::prefix('region')->group(function () {
    Route::post('/', [AddressController::class, 'region']); // 地区数据
    Route::post('/group', [AddressController::class, 'regionGroup']); // 地区分组数据
});
// 商品详情
Route::prefix('goods')->group(function () {
    Route::middleware('api.auth')->group(function () {
        Route::post('follow', [GoodsCollectController::class, 'follow']); // 关注商品
        Route::post('unfollow', [GoodsCollectController::class, 'unfollow']); // 取消关注商品
    });
    Route::get('/{no}', [GoodsController::class, 'detail'])->where('no', '^(?!follow$|unfollow$)[a-zA-Z0-9-_]+');
    Route::get('/{no}/check_number', [GoodsController::class, 'checkNumber'])->where('no', '^(?!follow$|unfollow$)[a-zA-Z0-9-_]+');
    Route::get('/{no}/{unique}', [GoodsController::class, 'skuItem'])->where('no', '^(?!follow$|unfollow$)[a-zA-Z0-9-_]+');
});
// 商品评价列表
Route::get('evaluate/goods', [EvaluateController::class, 'indexByGoods']);

/**
 * 登录可以访问路由.
 */
Route::middleware('api.auth')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']); // 退出登录
        Route::post('edit/password', [AuthController::class, 'editPassword']); // 修改密码
    });
    // 订单相关路由
    Route::prefix('order')->group(function () {
        Route::get('direct/init', [DoneController::class, 'directInit']);
        Route::post('direct/done', [DoneController::class, 'directDone']);
        Route::get('cart/init', [DoneController::class, 'cartInit']);
        Route::post('cart/done', [DoneController::class, 'cartDone']);
        Route::prefix('cash')->group(function () {
            Route::get('/', [PayController::class, 'index']);
            Route::post('wechat/pay', [PayController::class, 'wechatPay']);
        });
        Route::prefix('my')->group(function () {
            Route::get('index', [MyOrderIndexController::class, 'index']);
            Route::get('detail', [MyOrderIndexController::class, 'detail']);
            Route::post('destroy', [MyOrderIndexController::class, 'destroy']);
            Route::post('cancel', [MyOrderIndexController::class, 'cancel']);
            Route::get('address/edit', [MyOrderIndexController::class, 'addressEdit']);
            Route::post('address/update', [MyOrderIndexController::class, 'addressUpdate']);
        });

        // 申请售后
        Route::prefix('apply_refund')->group(function () {
            Route::get('verify', [ApplyRefundController::class, 'verify']); // 检测是否允许申请售后
            Route::get('init', [ApplyRefundController::class, 'init']); // 初始化申请售后
            Route::get('show', [ApplyRefundController::class, 'show']); // 根据售后类型回显数据
            Route::post('store', [ApplyRefundController::class, 'store']); // 提交申请售后
            Route::get('detail', [ApplyRefundController::class, 'detail']); // 售后详情
            Route::get('log', [ApplyRefundController::class, 'log']); // 协商历史
            Route::post('revoke', [ApplyRefundController::class, 'revoke']); // 撤销申请
        });
    });

    // 用户地址
    Route::prefix('address')->group(function () {
        Route::post('list', [AddressController::class, 'index']); // 收货地址
        Route::post('search', [AddressController::class, 'search']); // 搜索地址
        Route::post('show', [AddressController::class, 'show']); // 获取一条收货地址
        Route::post('default', [AddressController::class, 'setDefault']); // 设置默认地址
        Route::post('update', [AddressController::class, 'update']); // 添加|编辑 收货地址
        Route::post('destroy', [AddressController::class, 'destroy']); // 删除一条收货地址
        Route::post('batch_destroy', [AddressController::class, 'batchDestroy']); // 批量删除
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
        Route::post('empty_invalid', [CartController::class, 'emptyInvalid']);  // 清空失效
        Route::post('move_collect', [CartController::class, 'moveCollect']);    // 移入收藏
        Route::post('place_order', [CartController::class, 'placeOrder']);      // 去结算
    });
});
