<?php

use App\Http\Controllers\Manage;
use Illuminate\Support\Facades\Route;

// 移动端装修
Route::group(['prefix' => 'app_web_decoration'], function () {
    Route::any('index', [Manage\AppWebsiteDecorationController::class, 'index'])->name('manage.app_web_decoration.index');
    Route::any('children_index', [Manage\AppWebsiteDecorationController::class, 'children_index'])->name('manage.app_web_decoration.children_index');
    Route::any('copy', [Manage\AppWebsiteDecorationController::class, 'copy'])->name('manage.app_web_decoration.copy');
    Route::any('destroy', [Manage\AppWebsiteDecorationController::class, 'destroy'])->name('manage.app_web_decoration.destroy');
    Route::any('change_is_show', [Manage\AppWebsiteDecorationController::class, 'change_is_show'])->name('manage.app_web_decoration.change_is_show');
    Route::any('edit', [Manage\AppWebsiteDecorationController::class, 'edit'])->name('manage.app_web_decoration.edit');
    Route::any('store', [Manage\AppWebsiteDecorationController::class, 'store'])->name('manage.app_web_decoration.store');
    Route::any('decoration', [Manage\AppWebsiteDecorationController::class, 'decoration'])->name('manage.app_web_decoration.decoration');
    Route::any('decoration_store', [Manage\AppWebsiteDecorationController::class, 'decoration_store'])->name('manage.app_web_decoration.decoration_store');
    Route::any('get_content_data_by_alias', [Manage\AppWebsiteDecorationController::class, 'get_content_data_by_alias'])->name('manage.app_web_decoration.get_content_data_by_alias');
    Route::any('get_second_cats_news', [Manage\AppWebsiteDecorationController::class, 'get_second_cats_news'])->name('manage.app_web_decoration.get_second_cats_news');
    Route::any('get_hot_sale_goods_by_cat_id', [Manage\AppWebsiteDecorationController::class, 'get_hot_sale_goods_by_cat_id'])->name('manage.app_web_decoration.get_hot_sale_goods_by_cat_id');
    Route::any('router_options', [Manage\AppWebsiteDecorationController::class, 'router_options'])->name('manage.app_web_decoration.router_options');
    Route::any('get_options', [Manage\AppWebsiteDecorationController::class, 'get_options'])->name('manage.app_web_decoration.get_options');
    Route::any('router_search', [Manage\AppWebsiteDecorationController::class, 'router_search'])->name('manage.app_web_decoration.router_search');
    Route::any('get_cat_list', [Manage\AppWebsiteDecorationController::class, 'get_cat_list'])->name('manage.app_web_decoration.get_cat_list');
    Route::any('seller_router_options', [Manage\AppWebsiteDecorationController::class, 'seller_router_options'])->name('manage.app_web_decoration.seller_router_options');
    Route::any('seller_router_search', [Manage\AppWebsiteDecorationController::class, 'seller_router_search'])->name('manage.app_web_decoration.seller_router_search');
    Route::any('get_free_try_goods', [Manage\AppWebsiteDecorationController::class, 'get_free_try_goods'])->name('manage.app_web_decoration.get_free_try_goods');
    Route::any('get_charge_try_goods', [Manage\AppWebsiteDecorationController::class, 'get_charge_try_goods'])->name('manage.app_web_decoration.get_charge_try_goods');
});
