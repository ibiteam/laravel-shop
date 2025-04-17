<?php

namespace App\Http\Dao;

use App\Models\GoodsView;
use App\Models\User;

class GoodsViewDao
{
    /**
     * 添加商品访问记录.
     */
    public function store(int $goods_id, User $user): void
    {
        $goods_view = GoodsView::query()->firstOrNew(['goods_id' => $goods_id, 'user_id' => $user->id]);
        $goods_view->ip = get_request_ip();
        $goods_view->referer = get_source();

        if ($goods_view->exists) {
            $goods_view->updated_at = now();
        }

        $goods_view->save();
    }
}
