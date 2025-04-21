<?php

namespace App\Http\Dao;

use App\Models\ShipCompany;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class ShipCompanyDao
{
    /**
     * 获取启用的快递公司列表.
     */
    public function getListByEnable(): EloquentCollection|Collection
    {
        return ShipCompany::query()->whereStatus(true)->select(['id', 'name', 'code'])->latest()->get();
    }
}
