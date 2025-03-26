<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\CategoryDao;
use Illuminate\Http\Request;

/**
 * 分类.
 */
class CategoryController extends BaseController
{
    public function index(Request $request, CategoryDao $category_dao)
    {
        try {
            // 获取分类树状数据
            $categoryTree = $category_dao->getTreeList();

            return $this->success($categoryTree);
        } catch (\Throwable $throwable) {
            return $this->error('获取分类异常');
        }
    }
}
