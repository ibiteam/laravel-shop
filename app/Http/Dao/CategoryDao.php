<?php

namespace App\Http\Dao;

use App\Models\Category;

class CategoryDao
{
    /**
     * 获取树状分类.
     */
    public function getTreeList()
    {
        return Category::query()->with('allChildren')
            ->whereParentId(0)
            ->whereIsShow(Category::IS_SHOW_YES)
            ->orderByDesc('sort')
            ->get()->map(fn (Category $category) => $this->categoryFormat($category));
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
