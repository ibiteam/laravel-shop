<?php

namespace App\Services\Order;

use App\Exceptions\BusinessException;
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
     * 获取商品总价格.
     */
    public function getGoodsAmount(): float|int
    {
        return (float) to_number_format($this->getGoods()->price * $this->getBuyNumber());
    }

    /**
     * 获取商品总积分.
     */
    public function getGoodsIntegral(): float|int
    {
        return $this->getGoods()->integral * $this->getBuyNumber();
    }

    /**
     * 校验商品信息并赋值
     *
     * @return $this
     *
     * @throws BusinessException
     */
    public function validate(): self
    {
        $goods = $this->getGoods() ?: Goods::query()->with('skus')->withTrashed()->whereNo($this->getGoodsNo())->first();

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
            $order_ids = Order::query()->whereUserId($this->getUser()->id)->pluck('id');

            if ($order_ids->isNotEmpty()) {
                $sum = OrderDetail::query()->whereGoodsId($goods->id)->whereIn('order_id', $order_ids)->sum('goods_number');

                if ($sum >= $goods->quota_number) {
                    throw new BusinessException('该商品限购'.$goods->quota_number.'件，您已超过限制，请选择其他商品');
                }
            }
        }
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

            if ($this->getBuyNumber() >= $goods_sku->number) {
                throw new BusinessException('商品所选规格库存不足');
            }
            $goods->total = $goods_sku->number;
            // 设置商品规格信息
            $this->setGoodsSku($goods_sku);
        } else {
            if ($this->getBuyNumber() > $goods->total) {
                throw new BusinessException('商品库存不足');
            }
        }

        $this->setGoods($goods);

        return $this;
    }

    /**
     * 初始化回显商品数据.
     *
     * @throws BusinessException
     */
    public function initFormat(): array
    {
        $goods = $this->getGoods();
        $sku_data = '';

        foreach ($this->getSkuData() as $sku_datum) {
            $sku_data .= $sku_datum['key'].':'.$sku_datum['value'].';';
        }

        return [
            'no' => $goods->no,
            'name' => $goods->name,
            'sub_name' => $goods->sub_name,
            'label' => $goods->label,
            'thumb' => $goods->image,
            'price' => $goods->price,
            'integral' => $goods->integral,
            'unit' => $goods->unit,
            'number' => $goods->total,
            'buy_number' => $this->getBuyNumber(),
            'sku_id' => $this->getSkuId(),
            'sku_data' => $sku_data,
        ];
    }

    /**
     * 生成订单详情数据.
     *
     * @throws BusinessException
     */
    public function getOrderDetailFormat(): array
    {
        $goods = $this->getGoods();

        return [
            'goods_id' => $goods->id,
            'goods_no' => $goods->no,
            'goods_name' => $goods->name,
            'goods_number' => $this->getBuyNumber(),
            'goods_price' => $goods->price,
            'goods_integral' => $goods->integral,
            'goods_amount' => $this->getGoodsAmount(),
            'goods_unit' => $goods->unit,
            'goods_sku_id' => $this->getSkuId(),
            'goods_sku_value' => $this->getSkuData(),
        ];
    }

    /**
     * 是否下单减库存.
     */
    public function isDoneDecrementStock(): bool
    {
        return $this->getGoods()->isDoneDecrementStock();
    }

    /**
     * 减库存.
     */
    public function decrementStock(): void
    {
        if ($this->getGoodsSku() instanceof GoodsSku) {
            $this->getGoodsSku()->decrementStock($this->getBuyNumber());
        }

        $this->getGoods()->decrementStock($this->getBuyNumber());
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

    public function setGoods(Goods $goods): self
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

    public function getSkuData(): array
    {
        $sku_data = [];

        $goods_sku = $this->getGoodsSku();

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
