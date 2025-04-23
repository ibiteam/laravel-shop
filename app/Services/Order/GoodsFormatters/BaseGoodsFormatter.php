<?php

namespace App\Services\Order\GoodsFormatters;

use App\Enums\OrderStatusEnum;
use App\Exceptions\BusinessException;
use App\Models\Goods;
use App\Models\GoodsSku;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;

abstract class BaseGoodsFormatter implements GoodsFormatterInterface
{
    private int $buy_number; // 购买数量
    private User $user; // 当前用户
    private string $goods_no; // 商品编号
    private ?Goods $goods = null; // 商品模型
    private int $sku_id; // 商品规格ID
    private ?GoodsSku $goods_sku = null; // 商品规格
    private int $cart_id = 0; // 购物车ID
    private int|float $goods_amount; // 购买商品所需总金额
    private int|float $goods_integral; // 购买商品所需总积分
    private array $sku_value = []; // 商品规格值
    private string $goods_image = ''; // 商品图片
    private array $number_data = [
        'can_buy' => false,
        'total' => 0,
    ]; // 设置可以购买的最大数量以及是否可以购买

    /**
     * @throws BusinessException
     */
    public function validate(): GoodsFormatterInterface
    {
        $goods = $this->getGoods();

        if (! $goods instanceof Goods) {
            $goods = Goods::query()->with('skus')->withTrashed()->whereNo($this->getGoodsNo())->first();

            $this->setGoods($goods);
        }

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
            $order_ids = Order::query()->where('order_status', '!=', OrderStatusEnum::CANCELLED->value)->whereUserId($this->getUser()->id)->pluck('id');

            if ($order_ids->isNotEmpty()) {
                $sum = OrderDetail::query()->whereGoodsId($goods->id)->whereIn('order_id', $order_ids)->sum('goods_number');

                if ($sum >= $goods->quota_number) {
                    throw new BusinessException('该商品限购'.$goods->quota_number.'件，您已超过限制，请选择其他商品');
                }
            }
        }
        $this->setGoodsImage($goods->image);

        return $this;
    }

    public function settlementFormat(): array
    {
        $goods = $this->getGoods();
        $goods_sku = $this->getGoodsSku();

        $sku_value = '';

        if ($goods_sku) {
            foreach ($this->getSkuValue() as $sku_datum) {
                $sku_value .= $sku_datum['key'].':'.$sku_datum['value'].';';
            }
            $tmp_price = $goods_sku->price;
            $tmp_integral = $goods_sku->integral;
            $tmp_number = $goods_sku->number;
        } else {
            $tmp_price = $goods->price;
            $tmp_integral = $goods->integral;
            $tmp_number = $goods->total;
        }

        return [
            'no' => $goods->no,
            'name' => $goods->name,
            'sub_name' => $goods->sub_name,
            'label' => $goods->label,
            'thumb' => $this->getGoodsImage(),
            'price' => $tmp_price,
            'integral' => $tmp_integral,
            'unit' => $goods->unit,
            'number' => $tmp_number,
            'buy_number' => $this->getBuyNumber(),
            'sku_id' => $this->getSkuId(),
            'sku_data' => $sku_value,
        ];
    }

    public function buildOrderItem(): array
    {
        $goods = $this->getGoods();
        $goods_sku = $this->getGoodsSku();

        if ($goods_sku) {
            $tmp_price = $goods_sku->price;
            $tmp_integral = $goods_sku->integral;
        } else {
            $tmp_price = $goods->price;
            $tmp_integral = $goods->integral;
        }

        return [
            'goods_id' => $goods->id,
            'goods_no' => $goods->no,
            'goods_name' => $goods->name,
            'goods_number' => $this->getBuyNumber(),
            'goods_price' => $tmp_price,
            'goods_integral' => $tmp_integral,
            'goods_amount' => $this->getGoodsAmount(),
            'goods_unit' => $goods->unit ?: '',
            'goods_sku_id' => $this->getSkuId(),
            'goods_sku_value' => $this->getSkuValue(),
        ];
    }

    public function decrementStock(bool $payed = false): void
    {
        $goods = $this->getGoods();
        $goods_sku = $this->getGoodsSku();

        // 先判断 是否支付减库存 && 已支付
        if ($goods->isPayDecrementStock() && $payed) {
            if ($goods_sku instanceof GoodsSku) {
                $goods_sku->decrementStock($this->getBuyNumber());
            }

            $goods->decrementStock($this->getBuyNumber());

            return;
        }

        if ($goods_sku instanceof GoodsSku) {
            $goods_sku->decrementStock($this->getBuyNumber());
        }

        $goods->decrementStock($this->getBuyNumber());
    }

    public function getBuyNumber(): int
    {
        return $this->buy_number;
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

    public function getGoodsNo(): string
    {
        return $this->goods_no;
    }

    public function setGoodsNo(string $goods_no): self
    {
        $this->goods_no = $goods_no;

        return $this;
    }

    public function getGoods(): ?Goods
    {
        return $this->goods;
    }

    public function setGoods(?Goods $goods): self
    {
        $this->goods = $goods;

        return $this;
    }

    public function getSkuId(): int
    {
        return $this->sku_id;
    }

    public function setSkuId(int $sku_id): self
    {
        $this->sku_id = $sku_id;

        return $this;
    }

    public function getGoodsSku(): ?GoodsSku
    {
        return $this->goods_sku;
    }

    public function setGoodsSku(?GoodsSku $goods_sku): self
    {
        $this->goods_sku = $goods_sku;

        return $this;
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
        $this->number_data['can_buy'] = $can_buy;
        $this->number_data['total'] = $total;
    }

    public function getGoodsAmount(): float|int
    {
        return $this->goods_amount;
    }

    public function setGoodsAmount(float|int $goods_amount): void
    {
        $this->goods_amount = to_number_format($goods_amount);
    }

    public function getGoodsIntegral(): float|int
    {
        return $this->goods_integral;
    }

    public function setGoodsIntegral(float|int $goods_integral): void
    {
        $this->goods_integral = $goods_integral;
    }

    protected function getSkuValue(): array
    {
        return $this->sku_value;
    }

    protected function setSkuValue(array $sku_value): void
    {
        $this->sku_value = $sku_value;
    }

    protected function getGoodsImage(): string
    {
        return $this->goods_image;
    }

    protected function setGoodsImage(string $goods_image): void
    {
        $this->goods_image = $goods_image;
    }
}
