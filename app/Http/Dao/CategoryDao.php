<?php

namespace App\Http\Dao;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class CategoryDao
{
    /**
     * 获取树状分类.
     */
    public function getTreeList()
    {
        return Category::query()->with('allChildren')
            ->whereParentId(0)
            ->orderByDesc('sort')
            ->get()->map(fn (Category $category) => $this->categoryFormat($category));
    }

    /**
     * 获取显示的商品分类.
     */
    public function getShowTreeList(): EloquentCollection|Collection
    {
        return Category::query()->with('showAllChildren')
            ->whereParentId(0)
            ->whereIsShow(Category::IS_SHOW_YES)
            ->orderByDesc('sort')
            ->get()->map(fn (Category $category) => $this->showCategoryFormat($category));
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

    private function showCategoryFormat(Category $category): array
    {
        if ($category->showAllChildren->isEmpty()) {
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
            'logo' => $category->logo,
            'children' => $category->showAllChildren->map(fn (Category $category) => $this->showCategoryFormat($category))->toArray(),
        ];
    }
}
