<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\CategoryDao;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * 分类.
 */
class CategoryController extends BaseController
{
    public function index(Request $request, CategoryDao $category_dao)
    {
        try {
            // 获取所有分类数据
            $categories = Category::select(['id', 'parent_id', 'name', 'logo'])->get();

            // 构建分类树状结构
            $categoryTree = $category_dao->categoryTree($categories);

            return $this->success($categoryTree);
        } catch (\Throwable $throwable) {
            return $this->error('获取分类异常');
        }
    }
}
