<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\CategoryDao;

/**
 * 分类.
 */
class CategoryController extends BaseController
{
    public function index(CategoryDao $category_dao)
    {
        try {
            return $this->success($category_dao->getTreeList());
        } catch (\Throwable $throwable) {
            return $this->error('获取分类异常');
        }
    }
}
