<?php

namespace App\Http\Dao;

use App\Enums\ConstantEnum;
use App\Exceptions\BusinessException;
use App\Models\Goods;

class GoodsDao
{
    /**
     * 根据商品编号获取商品信息.
     *
     * @throws BusinessException
     */
    public function getInfoByNo(string $no): Goods
    {
        $goods = Goods::query()->whereNo($no)->first();

        if (! $goods instanceof Goods) {
            throw new BusinessException('商品不存在');
        }

        return $goods;
    }

    /**
     * 校验商品是否删除.
     *
     * @throws BusinessException
     */
    public function checkGoodsIsDestroy(Goods $goods): void
    {
        // 判断商品是否删除
        if ($goods->deleted_at) {
            throw new BusinessException('商品已删除', ConstantEnum::GOODS_DESTROY);
        }
    }
}
