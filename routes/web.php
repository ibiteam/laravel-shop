<?php

use App\Http\Controllers\Notify\WechatPayController;
use Illuminate\Support\Facades\Route;

Route::prefix('notify')->group(function () {
    Route::any('wechat/pay', [WechatPayController::class, 'index'])->name('notify.wechat.pay');
});

Route::get(config('app.manage_prefix').'/{any?}', function () {
    return view('manage');
})->where('any', '^(?!api|storage).*$');
