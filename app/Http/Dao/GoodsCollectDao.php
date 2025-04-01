<?php

namespace App\Http\Dao;

use App\Models\GoodsCollect;

class GoodsCollectDao
{
    /**
     * 根据用户id和商品id获取商品收藏信息.
     */
    public function getInfoByUserAndGoodsId(int $goods_id, int $user_id): ?GoodsCollect
    {
        return GoodsCollect::query()->whereUserId($user_id)->whereGoodsId($goods_id)->first();
    }

    /**
     * 添加关注商品
     */
    public function store(int $goods_id, int $user_id): GoodsCollect
    {
        return GoodsCollect::query()->create([
            'goods_id' => $goods_id,
            'user_id' => $user_id,
            'is_attention' => GoodsCollect::ATTENTION_YES,
        ]);
    }
}
