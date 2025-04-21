<?php

use App\Http\Controllers\Notify\WechatPayController;
use App\Http\Middleware\Api\AccessLogMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware([AccessLogMiddleware::class])->group(function () {
    require __DIR__.'/api_v1.php';
});

Route::prefix('manage')->middleware([])->group(function () {
    require __DIR__.'/api_manage.php';
});

Route::prefix('notify')->group(function () {
    Route::any('wechat/pay', [WechatPayController::class, 'notifyPay'])->name('notify.wechat.pay');
    Route::any('wechat/refund', [WechatPayController::class, 'notifyRefund'])->name('notify.wechat.refund');
});
