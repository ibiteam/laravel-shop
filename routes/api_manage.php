<?php

use App\Http\Controllers\Manage\HomeController;
use App\Http\Controllers\Manage\LoginController;
use App\Http\Controllers\Manage\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'showLoginForm'])->name('manage.login');
Route::post('login', [LoginController::class, 'login'])->name('manage.login.submit');

Route::middleware(['manage.auth', 'manage.access.record'])->group(function () {
    Route::post('upload', [UploadController::class, 'upload']);

    Route::prefix('home')->group(function () {
        Route::get('menus', [HomeController::class, 'menus']);  // 菜单权限
        Route::get('dashboard', [HomeController::class, 'dashboard']);  // 首页数据
        Route::post('collect_menu', [HomeController::class, 'collectMenu']); // 收藏菜单
        Route::get('clear_cache', [HomeController::class, 'clearCache']);   // 清除缓存
    });

    require __DIR__.'/manage/set.php';

    require __DIR__.'/manage/goods.php';

    require __DIR__.'/manage/user.php';
});
