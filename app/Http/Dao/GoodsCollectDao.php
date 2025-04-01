<?php

namespace App\Http\Dao;

use App\Models\GoodsCollect;

class GoodsCollectDao
{
    public function getInfoByUserAndGoodsId(int $goods_id, int $user_id): ?GoodsCollect
    {
        return GoodsCollect::query()->whereUserId($user_id)->whereGoodsId($goods_id)->first();
    }
}
