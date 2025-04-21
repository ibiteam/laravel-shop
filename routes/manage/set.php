<?php

use App\Http\Controllers\Manage\AdminOperationLogController;
use App\Http\Controllers\Manage\AdminUserController;
use App\Http\Controllers\Manage\AppDecorationController;
use App\Http\Controllers\Manage\AppServiceConfigController;
use App\Http\Controllers\Manage\AppServiceLogController;
use App\Http\Controllers\Manage\PaymentController;
use App\Http\Controllers\Manage\PermissionController;
use App\Http\Controllers\Manage\RoleController;
use App\Http\Controllers\Manage\RouterCategoryController;
use App\Http\Controllers\Manage\RouterController;
use App\Http\Controllers\Manage\ShipCompanyController;
use App\Http\Controllers\Manage\ShopConfigController;
use App\Http\Controllers\Manage\TransactionController;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

// 管理员列表
Route::prefix('admin_user')->group(function () {
    Route::middleware(['manage.permission:'.Permission::MANAGE_ADMIN_USER_INDEX])->group(function () {
        Route::get('/', [AdminUserController::class, 'index']);
        Route::get('roles', [AdminUserController::class, 'roles']);
    });
    Route::middleware(['manage.permission:'.Permission::MANAGE_ADMIN_USER_UPDATE])->group(function () {
        Route::post('update', [AdminUserController::class, 'update']);
        Route::post('change/field', [AdminUserController::class, 'changeField']);
    });
});

// 权限菜单
Route::prefix('permission')->group(function () {
    Route::middleware(['manage.permission:'.Permission::MANAGE_PERMISSION_INDEX])->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name(Permission::MANAGE_PERMISSION_INDEX);
    });
    Route::middleware(['manage.permission:'.Permission::MANAGE_PERMISSION_UPDATE])->group(function () {
        Route::post('store', [PermissionController::class, 'store']);
    });
});

// 角色管理
Route::prefix('role')->group(function () {
    Route::middleware(['manage.permission:'.Permission::MANAGE_ROLE_INDEX])->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name(Permission::MANAGE_ROLE_INDEX);
        Route::get('info', [RoleController::class, 'info']);
    });
    Route::middleware(['manage.permission:'.Permission::MANAGE_ROLE_UPDATE])->group(function () {
        Route::post('store', [RoleController::class, 'store']);
        Route::post('change_show', [RoleController::class, 'changeShow']);
    });
    Route::middleware(['manage.permission:'.Permission::MANAGE_ROLE_DELETE])->group(function () {
        Route::post('destroy', [RoleController::class, 'destroy']);
    });
});

// 管理员日志
Route::prefix('admin_operation_log')->group(function () {
    Route::middleware(['manage.permission:'.Permission::MANAGE_ADMIN_OPERATION_LOG_INDEX])->group(function () {
        Route::get('/', [AdminOperationLogController::class, 'index'])->name(Permission::MANAGE_ADMIN_OPERATION_LOG_INDEX);
    });
});

Route::prefix('set')->group(function () {
    // 商店设置
    Route::prefix('shop_config')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_SHOP_CONFIG_INDEX])->group(function () {
            Route::get('/', [ShopConfigController::class, 'index'])->name(Permission::MANAGE_SHOP_CONFIG_INDEX);
            Route::get('search_article', [ShopConfigController::class, 'searchArticle']);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_SHOP_CONFIG_UPDATE])->group(function () {
            Route::post('update', [ShopConfigController::class, 'update']);
        });
    });

    // 访问地址分类
    Route::prefix('router_category')->group(function () {
        Route::get('tree', [RouterCategoryController::class, 'getTreeList']);  // 访问地址弹窗 左侧树状数据
        Route::middleware(['manage.permission:'.Permission::MANAGE_ROUTER_CATEGORY_INDEX])->group(function () {
            Route::get('/', [RouterCategoryController::class, 'index'])->name(Permission::MANAGE_ROUTER_CATEGORY_INDEX);
            Route::get('info', [RouterCategoryController::class, 'info']);
            Route::get('pages', [RouterCategoryController::class, 'getPages']);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_ROUTER_CATEGORY_UPDATE])->group(function () {
            Route::post('store', [RouterCategoryController::class, 'store']);
            Route::post('change_show', [RouterCategoryController::class, 'changeShow']);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_ROUTER_CATEGORY_DELETE])->group(function () {
            Route::post('destroy', [RouterCategoryController::class, 'destroy']);
        });
    });

    // 访问地址
    Route::prefix('router')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_ROUTER_INDEX])->group(function () {
            Route::get('/', [RouterController::class, 'index'])->name(Permission::MANAGE_ROUTER_INDEX);
            Route::get('categories', [RouterController::class, 'categories']);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_ROUTER_UPDATE])->group(function () {
            Route::post('store', [RouterController::class, 'store']);
            Route::post('change_show', [RouterController::class, 'changeShow']);
        });
    });

    // 外部服务
    Route::prefix('app_service')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_APP_SERVICE_CONFIG_INDEX])->group(function () {
            Route::get('/', [AppServiceConfigController::class, 'index'])->name(Permission::MANAGE_APP_SERVICE_CONFIG_INDEX);
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_APP_SERVICE_CONFIG_UPDATE])->group(function () {
            Route::post('update', [AppServiceConfigController::class, 'update']);
            Route::post('toggle/status', [AppServiceConfigController::class, 'toggleStatus']);
        });
    });
    // 外部服务日志
    Route::prefix('app_service_log')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_APP_SERVICE_LOG_INDEX])->group(function () {
            Route::get('/', [AppServiceLogController::class, 'index'])->name(Permission::MANAGE_APP_SERVICE_LOG_INDEX);
        });
    });

    // 支付方式
    Route::prefix('payment')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name(Permission::MANAGE_PAYMENT_INDEX)->middleware('manage.permission');
        Route::middleware('manage.permission:'.Permission::MANAGE_PAYMENT_UPDATE)->group(function () {
            Route::post('update', [PaymentController::class, 'update']);
            Route::post('change/field', [PaymentController::class, 'changeField']);
        });
        Route::prefix('transaction')->group(function () {
            Route::get('/', [TransactionController::class, 'index'])->name(Permission::MANAGE_TRANSACTION_INDEX)->middleware('manage.permission');
            Route::post('refund', [TransactionController::class, 'refund'])->name(Permission::MANAGE_TRANSACTION_REFUND)->middleware('manage.permission');
        });
    });

    // 配送管理-快递公司
    Route::prefix('ship_company')->group(function () {
        Route::get('/', [ShipCompanyController::class, 'index'])->name(Permission::MANAGE_SHIP_COMPANY_INDEX)->middleware('manage.permission');
        Route::middleware('manage.permission:'.Permission::MANAGE_SHIP_COMPANY_UPDATE)->group(function () {
            Route::get('edit', [ShipCompanyController::class, 'edit']);
            Route::post('update', [ShipCompanyController::class, 'update']);
            Route::post('change_status', [ShipCompanyController::class, 'changeStatus']);
        });
    });

    // 移动端装修
    Route::prefix('app_decoration')->group(function () {
        Route::middleware(['manage.permission:'.Permission::MANAGE_APP_DECORATION])->group(function () {
            Route::get('/', [AppDecorationController::class, 'index'])->name(Permission::MANAGE_APP_DECORATION); // 移动端装修
            Route::get('/decoration', [AppDecorationController::class, 'decoration']); // 移动端装修
            Route::post('/goods/list', [AppDecorationController::class, 'goodsList']); // 商品推荐组件 - 弹窗中商品列表
            Route::post('/goods/import', [AppDecorationController::class, 'importGoods']); // 商品推荐组件 - 商品导入
            Route::get('/recommend/data', [AppDecorationController::class, 'recommendData']); // 为您推荐组件数据
            Route::post('/goods/intelligent', [AppDecorationController::class, 'goodsForIntelligent']); // 商品推荐组件 - 智能推荐数据
            Route::get('/history', [AppDecorationController::class, 'decorationHistory']); // 装修历史记录
            Route::post('/history/restore', [AppDecorationController::class, 'historyRestore']); // 还原装修历史
        });
        Route::middleware(['manage.permission:'.Permission::MANAGE_MATERIAL_CENTER_UPDATE])->group(function () {
            Route::post('/decoration/save', [AppDecorationController::class, 'decorationSave']); // 移动端装修
        });
    });
});
