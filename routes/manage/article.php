<?php

use App\Http\Controllers\Manage\ArticleCategoryController;
use App\Http\Controllers\Manage\ArticleController;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

Route::prefix('article')->group(function () {
    // 文章列表
    Route::middleware(['manage.permission:'.Permission::MANAGE_ARTICLE_INDEX])->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->name(Permission::MANAGE_ARTICLE_INDEX);
        Route::get('info', [ArticleController::class, 'info']);
    });
    Route::middleware(['manage.permission:'.Permission::MANAGE_ARTICLE_UPDATE])->group(function () {
        Route::post('update', [ArticleController::class, 'update']);
        Route::post('change_field', [ArticleController::class, 'changeField']);
        Route::post('copy', [ArticleController::class, 'copy']);
        Route::post('update_cover', [ArticleController::class, 'updateCover']);
        Route::post('delete_cover', [ArticleController::class, 'deleteCover']);
    });
    Route::middleware(['manage.permission:'.Permission::MANAGE_ARTICLE_DELETE])->group(function () {
        Route::post('destroy', [ArticleController::class, 'destroy']);
    });

    // 文章分类
    Route::prefix('category')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_ARTICLE_CATEGORY_INDEX])->group(function () {
            Route::get('/', [ArticleCategoryController::class, 'index'])->name(Permission::MANAGE_ARTICLE_CATEGORY_INDEX);
            Route::get('info', [ArticleCategoryController::class, 'info']);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_ARTICLE_CATEGORY_UPDATE])->group(function () {
            Route::post('update', [ArticleCategoryController::class, 'update']);
            Route::post('change_show', [ArticleCategoryController::class, 'changeShow']);
            Route::post('move', [ArticleCategoryController::class, 'move']);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_ARTICLE_CATEGORY_DELETE])->group(function () {
            Route::post('destroy', [ArticleCategoryController::class, 'destroy']);
        });
    });
});
