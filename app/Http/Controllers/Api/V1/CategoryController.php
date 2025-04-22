<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\CategoryDao;
use Illuminate\Support\Facades\Log;

/**
 * 商品分类.
 */
class CategoryController extends BaseController
{
    public function index(CategoryDao $category_dao)
    {
        try {
            return $this->success($category_dao->getTreeList());
        } catch (\Throwable $throwable) {
            Log::error('获取商品分类异常'.$throwable->getMessage());
            return $this->error('获取商品分类异常');
        }
    }
}
