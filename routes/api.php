<?php

use App\Http\Controllers\Home\RegionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware([])->group(function () {
    require __DIR__.'/api_v1.php';
});

Route::prefix('pc')->middleware([])->group(function () {
    Route::get('region', [RegionController::class, 'getRegion']);  // 获取省市区信息

    require __DIR__.'/api_home.php';
});

