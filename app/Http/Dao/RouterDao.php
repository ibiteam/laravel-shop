<?php

namespace App\Http\Dao;

use App\Models\Router;
use App\Models\RouterCategory;
use Illuminate\Database\Eloquent\Collection;

class RouterDao
{
    /**
     * 获取路由分类.
     */
    public function categories(): Collection|\Illuminate\Support\Collection
    {
        return RouterCategory::query()
            ->whereIsShow(RouterCategory::IS_SHOW_YES)
            ->orderBy('sort', 'desc')
            ->get(['id as value', "name as label"]);
    }
}
