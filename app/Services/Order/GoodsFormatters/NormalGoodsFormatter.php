<?php

namespace App\Services\Order\GoodsFormatters;

use App\Exceptions\BusinessException;
use App\Exceptions\ProcessDataException;
use App\Http\Dao\GoodsSpecValueDao;
use App\Models\GoodsSku;
use App\Models\GoodsSpecValue;

class NormalGoodsFormatter extends BaseGoodsFormatter
{
    /**
     * @throws ProcessDataException
     * @throws BusinessException
     */
    public function validate(): GoodsFormatterInterface
    {
        parent::validate();

        $goods = $this->getGoods();
        $sku_id = $this->getSkuId();

        // 校验商品规格
        if ($goods->skus->isEmpty() && $sku_id) {
            throw new BusinessException('商品规格错误，请重新选择');
        }

        if ($goods->skus->isNotEmpty() && ! $sku_id) {
            throw new BusinessException('商品规格错误，请重新选择~');
        }

        // 校验商品规格库存
        if ($sku_id) {
            $goods_sku = $goods->skus()->whereId($sku_id)->first();

            if (! $goods_sku instanceof GoodsSku) {
                throw new BusinessException('商品所选规格不存在，请重新选择');
            }

            if ($goods_sku->is_show !== GoodsSku::SHOW) {
                throw new BusinessException('商品所选规格已下架，请重新选择');
            }

            if ($goods_sku->number <= 0) {
                throw new BusinessException('商品所选规格库存不足，请重新选择');
            }

            if ($this->getBuyNumber() > $goods_sku->number) {
                $this->setNumberData(false, $goods_sku->number);

                $tmp_message = $goods_sku->number.$goods->unit;

                throw new ProcessDataException("库存数量只有{$tmp_message}，您最多只能购买{$tmp_message}", $this->getNumberData());
            }
            $this->setNumberData(true, $goods_sku->number);

            $sku_value = [];

            $spec_value_ids = $goods_sku->explodeSkuValue();
            $spec_values = app(GoodsSpecValueDao::class)->getInfoByIds($spec_value_ids, $goods_sku->goods_id);

            foreach ($spec_value_ids as $key => $spec_value_id) {
                $spec_value = $spec_values->where('id', $spec_value_id)->first();

                if (! $spec_value instanceof GoodsSpecValue) {
                    throw new BusinessException('商品规格值不存在');
                }

                if ($key === 0 && $spec_value->thumb) {
                    $this->setGoodsImage($spec_value->thumb);
                }
                $sku_value[] = ['key' => $spec_value->spec?->name, 'value' => $spec_value->value];
            }
            $this->setSkuValue($sku_value);
            // 设置商品规格信息
            $this->setGoodsSku($goods_sku);
            $this->setGoodsTotalIntegral((int) ($goods_sku->integral * $this->getBuyNumber()));
            $this->setGoodsAmount((float) to_number_format($goods_sku->price * $this->getBuyNumber()));
        } else {
            if ($goods->total <= 0) {
                throw new BusinessException('商品库存不足，请重新选择');
            }

            if ($this->getBuyNumber() > $goods->total) {
                $this->setNumberData(false, $goods->total);

                $tmp_message = $goods->total.$goods->unit;

                throw new ProcessDataException("库存数量只有{$tmp_message}，您最多只能购买{$tmp_message}", $this->getNumberData());
            }

            $this->setNumberData(true, $goods->total);
            $this->setGoodsTotalIntegral((int) ($goods->integral * $this->getBuyNumber()));
            $this->setGoodsAmount((float) to_number_format($goods->price * $this->getBuyNumber()));
        }

        return $this;
    }
}
