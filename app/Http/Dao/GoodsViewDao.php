<?php

namespace App\Http\Dao;

use App\Models\GoodsView;

class GoodsViewDao
{
    /**
     * 添加商品访问记录
     */
    public function store(int $goods_id, int $user_id)
    {
        $goods_view = new GoodsView();
        if ($goods_view->whereGoodsId($goods_id)->whereUserId($user_id)->exists()) {
            $goods_view->updated_at = now();
        } else {
            $goods_view->goods_id = $goods_id;
            $goods_view->user_id = $user_id;
        }
        $goods_view->ip = get_request_ip();
        $goods_view->referer = get_source();

        return $goods_view->save();
    }
}
