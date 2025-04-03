<?php

use App\Http\Controllers\Manage\MaterialFileController;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

Route::prefix('tool')->group(function () {
    // 素材中心
    Route::prefix('material')->group(function () {
        Route::middleware(['manage.permission:' . Permission::MANAGE_MATERIAL_CENTER])->group(function () {
            Route::get('/', [MaterialFileController::class, 'index'])->name(Permission::MANAGE_MATERIAL_CENTER); // 素材列表
            Route::group(['prefix' => 'folder'], function () {
                Route::get('/', [MaterialFileController::class, 'folderList']); // 文件夹列表
                Route::get('list', [MaterialFileController::class, 'folderListForDirType']); // 上级文件夹
            });
        });
        Route::middleware(['manage.permission:' . Permission::MANAGE_MATERIAL_CENTER_UPDATE])->group(function () {
            Route::post('/rename', [MaterialFileController::class, 'rename']); // 修改素材文件名
            Route::post('/new/folder', [MaterialFileController::class, 'newFolder']); // 新建文件夹
            Route::post('/move', [MaterialFileController::class, 'move']);// 移动
            Route::post('/batch/move', [MaterialFileController::class, 'batchMove']);// 批量移动
            Route::post('/upload', [MaterialFileController::class, 'upload']);// 上传素材
        });
        Route::middleware(['manage.permission:' . Permission::MANAGE_MATERIAL_CENTER_DELETE])->group(function () {
            Route::post('/destory', [MaterialFileController::class, 'destory']);// 删除
            Route::post('/batch/destory', [MaterialFileController::class, 'batchDestroy']);// 批量删除
        });
    });
});
