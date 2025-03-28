<?php

use App\Http\Controllers\Manage\LoginController;
use App\Http\Controllers\Manage\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'showLoginForm'])->name('manage.login');
Route::post('login', [LoginController::class, 'login'])->name('manage.login.submit');

Route::middleware('manage.auth')->group(function () {
    Route::post('upload', [UploadController::class, 'upload']);

    require __DIR__.'/manage/goods.php';
});
