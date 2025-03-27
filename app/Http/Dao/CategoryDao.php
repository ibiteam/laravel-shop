<?php

namespace App\Http\Dao;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class CategoryDao
{
    /**
     * 获取树状分类.
     */
    public function getTreeList(): EloquentCollection|Collection
    {
        return Category::query()
            ->with('allChildren')
            ->whereParentId(0)
            ->get()
            ->map(fn (Category $category) => $this->categoryFormat($category));
    }

    /**
     * 获取顶级分类.
     *
     * @param bool $is_show 是否只获取显示的顶级分类
     */
    public function getTopCategory(bool $is_show = false): EloquentCollection|Collection
    {
        return Category::query()
            ->whereParentId(0)
            ->when($is_show, fn (Builder $query) => $query->where('is_show', $is_show))
            ->select(['parent_id', 'name', 'title', 'keywords', 'description', 'logo', 'sort', 'is_show'])
            ->get();
    }

    /**
     * 获取分类信息.
     */
    public function getInfoById(int $id): ?Category
    {
        return Category::query()->whereId($id)->first();
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
            'logo' => $category->logo,
            'children' => $category->allChildren->map(fn (Category $category) => $this->categoryFormat($category))->toArray(),
        ];
    }
}
