<?php

use App\Http\Controllers\Seller\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    // 首页
    Route::prefix('home')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
    });
});


