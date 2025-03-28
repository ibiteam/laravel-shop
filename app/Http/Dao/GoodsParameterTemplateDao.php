<?php

namespace App\Http\Dao;

use App\Models\GoodsParameterTemplate;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class GoodsParameterTemplateDao
{
    /**
     * 商品参数模板列表.
     */
    public function list(): EloquentCollection|Collection
    {
        return GoodsParameterTemplate::query()->select(['id', 'name', 'values'])->orderByDesc('id')->get();
    }
}
