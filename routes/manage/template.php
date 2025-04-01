<?php

use App\Http\Controllers\Manage;
use Illuminate\Support\Facades\Route;

// 移动端装修
Route::group(['prefix' => 'app_web_decoration'], function () {
    Route::any('index', [Manage\AppWebsiteDecorationController::class, 'index']);
    Route::any('children_index', [Manage\AppWebsiteDecorationController::class, 'childrenIndex']);
    Route::any('copy', [Manage\AppWebsiteDecorationController::class, 'copy']);
    Route::any('destroy', [Manage\AppWebsiteDecorationController::class, 'destroy']);
    Route::any('change_is_show', [Manage\AppWebsiteDecorationController::class, 'changeIsShow']);
    Route::any('edit', [Manage\AppWebsiteDecorationController::class, 'edit']);
    Route::any('store', [Manage\AppWebsiteDecorationController::class, 'store']);
    Route::any('decoration', [Manage\AppWebsiteDecorationController::class, 'decoration']);
    Route::any('decoration_store', [Manage\AppWebsiteDecorationController::class, 'decorationStore']);
    Route::any('get_content_data_by_alias', [Manage\AppWebsiteDecorationController::class, 'getContentDataByAlias']);
    Route::any('get_second_cats_news', [Manage\AppWebsiteDecorationController::class, 'getSecondCatsNews']);
    Route::any('get_hot_sale_goods_by_cat_id', [Manage\AppWebsiteDecorationController::class, 'getHotSaleGoodsByCatId']);
    Route::any('router_options', [Manage\AppWebsiteDecorationController::class, 'routerOptions']);
    Route::any('get_options', [Manage\AppWebsiteDecorationController::class, 'getOptions']);
    Route::any('router_search', [Manage\AppWebsiteDecorationController::class, 'routerSearch']);
    Route::any('get_cat_list', [Manage\AppWebsiteDecorationController::class, 'getCatList']);
    Route::any('seller_router_options', [Manage\AppWebsiteDecorationController::class, 'sellerRouterOptions']);
    Route::any('seller_router_search', [Manage\AppWebsiteDecorationController::class, 'sellerRouterSearch']);
    Route::any('get_free_try_goods', [Manage\AppWebsiteDecorationController::class, 'getFreeTryGoods']);
    Route::any('get_charge_try_goods', [Manage\AppWebsiteDecorationController::class, 'getChargeTryGoods']);
 });

// 素材中心
Route::group(['prefix' => 'material'], function () {
    Route::get('/', [Manage\MaterialFileController::class, 'index']); // 素材列表
    Route::post('/rename', [Manage\MaterialFileController::class, 'rename']); // 修改素材文件名
    Route::post('/new/file', [Manage\MaterialFileController::class, 'newFile']); // 新建文件 - 素材
    Route::post('/new/folder', [Manage\MaterialFileController::class, 'newFolder']); // 新建文件夹
    Route::post('/destory', [Manage\MaterialFileController::class, 'destory']);// 删除
    Route::post('/batch/destory', [Manage\MaterialFileController::class, 'batchDestroy']);// 批量删除
    Route::post('/move', [Manage\MaterialFileController::class, 'move']);// 移动
    Route::post('/batch/move', [Manage\MaterialFileController::class, 'batchMove']);// 批量移动
    Route::post('/upload', [Manage\MaterialFileController::class, 'upload']);// 上传素材
    Route::group(['prefix' => 'folder'], function () {
        Route::get('/', [Manage\MaterialFileController::class, 'folderList']); // 文件夹列表
        Route::get('list', [Manage\MaterialFileController::class, 'folderListForDirType']); // 上级文件夹
    });
});
