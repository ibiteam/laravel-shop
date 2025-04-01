<?php

namespace App\Http\Dao;

use App\Models\Goods;

class GoodsDao
{
    /**
     * 根据商品编号获取商品信息.
     */
    public function getInfoByNo(string $no): ?Goods
    {
        return Goods::query()->whereNo($no)->first();
    }
}
