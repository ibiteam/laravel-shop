<?php

use App\Http\Controllers\Manage\AccessStatisticController;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

Route::prefix('access_statistic')->group(function () {
    Route::middleware(['manage.permission:'.Permission::MANAGE_ACCESS_STATISTIC_INDEX])->group(function () {
        Route::get('/', [AccessStatisticController::class, 'index'])->name(Permission::MANAGE_ACCESS_STATISTIC_INDEX);
    });
});
