<?php

namespace App\Http\Dao;

use App\Models\GoodsSkuTemplate;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class GoodsSkuTemplateDao
{
    /**
     * 商品参数模板列表.
     */
    public function list(): EloquentCollection|Collection
    {
        return GoodsSkuTemplate::query()->select(['id', 'name', 'values', 'updated_at'])->orderByDesc('id')->get();
    }
}
