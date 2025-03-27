<?php

namespace App\Http\Controllers\Manage;

use App\Http\Dao\CategoryDao;
use App\Models\Category;
use Illuminate\Http\Request;

class GoodsCategoryController extends BaseController
{
    /**
     * 商品分类列表.
     */
    public function index(Request $request, CategoryDao $category_dao)
    {
        if ($request->expectsJson()) {
            return $this->success($category_dao->getTreeList());
        }

        return view('manage.goods.category');
    }

    public function create(Request $request)
    {
        $top_categories = Category::query()->whereParentId(0)->get();

        return $this->success($top_categories);
    }

    public function edit(Request $request, CategoryDao $category_dao)
    {
        $id = $request->input('id');

        if (! $id || ! is_int($id)) {
            return $this->error('请求参数错误');
        }
        $goods_category = $category_dao->getInfoById($id);

        if (! $goods_category instanceof Category) {
            return $this->error('商品分类不存在');
        }
    }
}
