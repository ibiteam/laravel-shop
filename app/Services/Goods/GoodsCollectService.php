<?php

namespace App\Services\Goods;

use App\Enums\ConstantEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\GoodsCollectDao;
use App\Models\Goods;
use App\Models\GoodsCollect;
use App\Models\User;

class GoodsCollectService
{
    /**
     * 关注商品
     *
     * @throws BusinessException
     */
    public function follow(Goods $goods, User $user): void
    {
        $goods_collect = app(GoodsCollectDao::class)->getInfoByUserAndGoodsId($goods->id, $user->id);

        if ($goods_collect instanceof GoodsCollect) {
            if ($goods_collect->is_attention == GoodsCollect::ATTENTION_YES) {
                throw new BusinessException('您已经关注过该商品', ConstantEnum::SUCCESS);
            }

            if (! $goods_collect->update(['is_attention' => GoodsCollect::ATTENTION_YES])) {
                throw new BusinessException('关注失败');
            }

            return;
        }

        if (! app(GoodsCollectDao::class)->store($goods->id, $user->id)) {
            throw new BusinessException('关注失败');
        }
    }

    /**
     * 取消关注商品.
     *
     * @throws BusinessException
     */
    public function unfollow(Goods $goods, User $user): void
    {
        $goods_collect = app(GoodsCollectDao::class)->getInfoByUserAndGoodsId($goods->id, $user->id);

        if (! $goods_collect instanceof GoodsCollect) {
            return;
        }

        if ($goods_collect->is_attention == GoodsCollect::ATTENTION_NO) {
            throw new BusinessException('您已取消关注该商品', ConstantEnum::SUCCESS);
        }

        if (! $goods_collect->update(['is_attention' => GoodsCollect::ATTENTION_NO])) {
            throw new BusinessException('取消关注失败');
        }
    }
}
