<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware([])->group(function () {
    require __DIR__.'/api_v1.php';
});

Route::prefix('seller')->middleware([])->group(function () {
    require __DIR__.'/api_seller.php';
});
Route::prefix('pc')->middleware([])->group(function () {
    require __DIR__.'/api_home.php';
});

