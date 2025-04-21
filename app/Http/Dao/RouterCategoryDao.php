<?php

namespace App\Http\Dao;

use App\Models\RouterCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class RouterCategoryDao
{
    /**
     * 获取树状分类.
     */
    public function getTreeList(): EloquentCollection|\Illuminate\Support\Collection
    {
        return RouterCategory::query()->with('allChildren')
            ->whereParentId(0)
            ->whereIsShow(RouterCategory::IS_SHOW_YES)
            ->orderByDesc('sort')
            ->get()->map(fn (RouterCategory $router_category) => $this->routerCategoryFormat($router_category));
    }

    /**
     * 递归处理树状结构数据.
     */
    private function routerCategoryFormat(RouterCategory $router_category): array
    {
        if ($router_category->allChildren->isEmpty()) {
            return [
                'id' => $router_category->id,
                'parent_id' => $router_category->parent_id,
                'name' => $router_category->name,
                'type' => $router_category->type,
                'page_name' => $router_category->page_name,
            ];
        }

        return [
            'id' => $router_category->id,
            'parent_id' => $router_category->parent_id,
            'name' => $router_category->name,
            'type' => $router_category->type,
            'page_name' => $router_category->page_name,
            'children' => $router_category->allChildren->map(fn (RouterCategory $router_category) => $this->routerCategoryFormat($router_category))->toArray(),
        ];
    }
}
