<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware([])->group(function () {
    require __DIR__.'/api_v1.php';
});
