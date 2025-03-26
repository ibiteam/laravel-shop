<?php

use App\Http\Controllers\Seller\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\UploadController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('upload', [UploadController::class, 'upload']);
    // 首页
    Route::prefix('home')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
    });
});


