<?php

namespace App\Services\Order;

use App\Enums\OrderStatusEnum;
use App\Exceptions\BusinessException;
use App\Exceptions\ProcessDataException;
use App\Http\Dao\GoodsSpecValueDao;
use App\Models\Goods;
use App\Models\GoodsSku;
use App\Models\GoodsSpecValue;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;

class GoodsFormatter
{
    /**
     * @var int 购买数量
     */
    private int $buy_number;

    /**
     * @var User 当前用户
     */
    private User $user;

    /**
     * @var string 商品编号
     */
    private string $goods_no;

    /**
     * @var Goods|null 商品实例
     */
    private ?Goods $goods = null;

    /**
     * @var int 商品规格ID
     */
    private int $sku_id;

    /**
     * @var GoodsSku|null 商品规格
     */
    private ?GoodsSku $goods_sku = null;

    /**
     * @var int 购物车ID
     */
    private int $cart_id = 0;

    /**
     * @var array 设置可以购买的最大数量以及是否可以购买
     */
    private array $number_data;

    private int|float $goods_amount;

    private int|float $goods_integral;

    private array $sku_value = [];

    private string $goods_image = '';

    /**
     * 校验商品信息并赋值
     *
     * @return $this
     *
     * @throws BusinessException
     * @throws ProcessDataException
     */
    public function validate(): self
    {
        $goods = $this->goods ?: Goods::query()->with('skus')->withTrashed()->whereNo($this->goods_no)->first();

        if (! $goods instanceof Goods) {
            throw new BusinessException('商品不存在');
        }

        if ($goods->trashed()) {
            throw new BusinessException('商品过期不存在');
        }

        if ($goods->status !== Goods::STATUS_ON_SALE) {
            throw new BusinessException('商品已下架，请选择其他商品');
        }

        if ($goods->can_quota === Goods::QUOTA) {
            $order_ids = Order::query()->where('order_status', '!=', OrderStatusEnum::CANCELLED->value)->whereUserId($this->user->id)->pluck('id');

            if ($order_ids->isNotEmpty()) {
                $sum = OrderDetail::query()->whereGoodsId($goods->id)->whereIn('order_id', $order_ids)->sum('goods_number');

                if ($sum >= $goods->quota_number) {
                    throw new BusinessException('该商品限购'.$goods->quota_number.'件，您已超过限制，请选择其他商品');
                }
            }
        }
        $sku_id = $this->sku_id;

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

            if ($this->buy_number > $goods_sku->number) {
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
                    $this->goods_image = $spec_value->thumb;
                }
                $sku_value[] = ['key' => $spec_value->spec?->name, 'value' => $spec_value->value];
            }
            $this->setSkuValue($sku_value);
            // 设置商品规格信息
            $this->setGoodsSku($goods_sku);
            $this->setGoodsIntegral((float) to_number_format($goods_sku->integral * $this->buy_number));
            $this->setGoodsAmount((float) to_number_format($goods_sku->price * $this->buy_number));
        } else {
            if ($goods->total <= 0) {
                throw new BusinessException('商品库存不足，请重新选择');
            }

            if ($this->buy_number > $goods->total) {
                $this->setNumberData(false, $goods->total);

                $tmp_message = $goods->total.$goods->unit;

                throw new ProcessDataException("库存数量只有{$tmp_message}，您最多只能购买{$tmp_message}", $this->getNumberData());
            }

            $this->setNumberData(true, $goods->total);
            $this->setGoodsIntegral((float) to_number_format($goods->integral * $this->buy_number));
            $this->setGoodsAmount((float) to_number_format($goods->price * $this->buy_number));
        }

        $this->setGoods($goods);

        return $this;
    }

    /**
     * 初始化回显商品数据.
     */
    public function initFormat(): array
    {
        $sku_value = '';

        if ($this->goods_sku) {
            foreach ($this->sku_value as $sku_datum) {
                $sku_value .= $sku_datum['key'].':'.$sku_datum['value'].';';
            }
            $tmp_price = $this->goods_sku->price;
            $tmp_integral = $this->goods_sku->integral;
            $tmp_number = $this->goods_sku->number;
        } else {
            $tmp_price = $this->goods->price;
            $tmp_integral = $this->goods->integral;
            $tmp_number = $this->goods->total;
        }

        return [
            'no' => $this->goods->no,
            'name' => $this->goods->name,
            'sub_name' => $this->goods->sub_name,
            'label' => $this->goods->label,
            'thumb' => $this->goods_image ?: $this->goods->image,
            'price' => $tmp_price,
            'integral' => $tmp_integral,
            'unit' => $this->goods->unit,
            'number' => $tmp_number,
            'buy_number' => $this->buy_number,
            'sku_id' => $this->sku_id,
            'sku_data' => $sku_value,
        ];
    }

    /**
     * 生成订单详情数据.
     */
    public function getOrderDetailFormat(): array
    {
        if ($this->goods_sku) {
            $tmp_price = $this->goods_sku->price;
            $tmp_integral = $this->goods_sku->integral;
        } else {
            $tmp_price = $this->goods->price;
            $tmp_integral = $this->goods->integral;
        }

        return [
            'goods_id' => $this->goods->id,
            'goods_no' => $this->goods->no,
            'goods_name' => $this->goods->name,
            'goods_number' => $this->buy_number,
            'goods_price' => $tmp_price,
            'goods_integral' => $tmp_integral,
            'goods_amount' => $this->getGoodsAmount(),
            'goods_unit' => $this->goods->unit ?: '',
            'goods_sku_id' => $this->sku_id,
            'goods_sku_value' => $this->sku_value,
        ];
    }

    /**
     * 减少库存.
     *
     * @param bool $payed 是否支付
     */
    public function decrementStock(bool $payed = false): void
    {
        // 先判断 是否支付减库存 && 已支付
        if ($this->goods->isPayDecrementStock()) {
            if ($payed) {
                if ($this->goods_sku instanceof GoodsSku) {
                    $this->goods_sku->decrementStock($this->buy_number);
                }

                $this->goods->decrementStock($this->buy_number);
            }

            return;
        }

        if ($this->goods_sku instanceof GoodsSku) {
            $this->goods_sku->decrementStock($this->buy_number);
        }

        $this->goods->decrementStock($this->buy_number);
    }

    public function setSkuValue(array $sku_value): self
    {
        $this->sku_value = $sku_value;

        return $this;
    }

    public function getGoodsAmount(): float|int
    {
        return $this->goods_amount;
    }

    public function setGoodsAmount(float|int $goods_amount): self
    {
        $this->goods_amount = $goods_amount;

        return $this;
    }

    public function getGoodsIntegral(): float|int
    {
        return $this->goods_integral;
    }

    public function setGoodsIntegral(float|int $goods_integral): self
    {
        $this->goods_integral = $goods_integral;

        return $this;
    }

    public function setBuyNumber(int $buy_number): self
    {
        $this->buy_number = $buy_number;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setGoodsNo(string $goods_no): self
    {
        $this->goods_no = $goods_no;

        return $this;
    }

    public function setGoods(Goods $goods): self
    {
        $this->goods = $goods;

        return $this;
    }

    public function setSkuId(int $sku_id): self
    {
        $this->sku_id = $sku_id;

        return $this;
    }

    public function setGoodsSku(?GoodsSku $goods_sku): void
    {
        $this->goods_sku = $goods_sku;
    }

    public function getCartId(): int
    {
        return $this->cart_id;
    }

    public function setCartId(int $cart_id): self
    {
        $this->cart_id = $cart_id;

        return $this;
    }

    public function getNumberData(): array
    {
        return $this->number_data;
    }

    public function setNumberData(bool $can_buy, int $total): void
    {
        $this->number_data = [
            'can_buy' => $can_buy,
            'total' => $total,
        ];
    }

    public function getSkuData(): array
    {
        $sku_data = [];

        $goods_sku = $this->goods_sku;

        if (! $goods_sku instanceof GoodsSku) {
            return $sku_data;
        }
        $spec_value_ids = $goods_sku->explodeSkuValue();
        $spec_values = app(GoodsSpecValueDao::class)->getInfoByIds($spec_value_ids, $goods_sku->goods_id);

        foreach ($spec_value_ids as $spec_value_id) {
            $spec_value = $spec_values->where('id', $spec_value_id)->first();

            if (! $spec_value instanceof GoodsSpecValue) {
                throw new BusinessException('商品规格值不存在');
            }
            $sku_data[] = ['key' => $spec_value->spec?->name, 'value' => $spec_value->value];
        }

        return $sku_data;
    }
}
