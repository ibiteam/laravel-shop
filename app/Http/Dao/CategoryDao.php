<?php

namespace App\Http\Dao;

use App\Models\Category;
use App\Models\Goods;

class CategoryDao
{
    /**
     * 获取商品分类（分类下没商品的不返回）.
     */
    public function getGoodsCategory()
    {
        $categories = Category::query()
            ->with('allChildren')
            ->whereParentId(0)
            ->whereIsShow(Category::IS_SHOW_YES)
            ->get();

        return $categories->map(fn (Category $category) => $this->goodsCategoryFormat($category))
            ->filter(function ($category) {
                return ! empty($category);
            });
    }

    /**
     * 获取树状分类.
     */
    public function getTreeList()
    {
        return Category::query()->with('allChildren')
            ->whereParentId(0)
            ->whereIsShow(Category::IS_SHOW_YES)
            ->get()->map(fn (Category $category) => $this->categoryFormat($category));
    }

    /**
     * 递归处理树状结构数据（分类下没商品的不返回）.
     */
    private function goodsCategoryFormat(Category $category): array
    {
        $children = $category->allChildren->map(fn (Category $child) => $this->goodsCategoryFormat($child))
            ->filter(function ($child) {
                return ! empty($child);
            });

        if ($children->isEmpty()) {
            // 检查分类是否有上架商品
            $category_goods_count = $category->goods()->where('status', Goods::STATUS_ON_SALE)->count();

            if ($category_goods_count > 0) {
                return [
                    'id' => $category->id,
                    'parent_id' => $category->parent_id,
                    'name' => $category->name,
                    'logo' => $category->logo,
                ];
            }

            return [];
        }

        return [
            'id' => $category->id,
            'parent_id' => $category->parent_id,
            'name' => $category->name,
            'children' => $children->toArray(),
        ];
    }

    /**
     * 递归处理树状结构数据.
     */
    private function categoryFormat(Category $category): array
    {
        if ($category->allChildren->isEmpty()) {
            return [
                'id' => $category->id,
                'parent_id' => $category->parent_id,
                'name' => $category->name,
                'logo' => $category->logo,
            ];
        }

        return [
            'id' => $category->id,
            'parent_id' => $category->parent_id,
            'name' => $category->name,
            'children' => $category->allChildren->map(fn (Category $category) => $this->categoryFormat($category))->toArray(),
        ];
    }
}
