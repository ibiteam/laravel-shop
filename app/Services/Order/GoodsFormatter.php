<?php

namespace App\Services\Order;

use App\Enums\OrderTypeEnum;
use App\Exceptions\BusinessException;
use App\Services\Order\GoodsFormatters\GoodsFormatterInterface;
use App\Services\Order\GoodsFormatters\NormalGoodsFormatter;

class GoodsFormatter
{
    protected GoodsFormatterInterface $formatter;

    /**
     * @throws BusinessException
     */
    public function __construct(OrderTypeEnum $order_type_enum = OrderTypeEnum::NORMAL)
    {
        $this->formatter = match ($order_type_enum) {
            OrderTypeEnum::NORMAL => new NormalGoodsFormatter,
            default => throw new BusinessException('订单类型错误'),
        };
    }

    /**
     * 获取商品格式化器.
     */
    public function getFormatter(): GoodsFormatterInterface
    {
        return $this->formatter;
    }

    public function settlementFormat(): array
    {
        return $this->formatter->settlementFormat();
    }

    /**
     * 订单商品信息格式化.
     */
    public function buildOrderItem(): array
    {
        return $this->formatter->buildOrderItem();
    }

    /**
     * 减少库存.
     *
     * @param bool $payed 是否支付
     */
    public function decrementStock(bool $payed = false): void
    {
        $this->formatter->decrementStock($payed);
    }

    /**
     * 获取购物车ID.
     */
    public function getCartId(): int
    {
        return $this->formatter->getCartId();
    }

    /**
     * 获取商品总金额.
     */
    public function getGoodsAmount(): float|int
    {
        return $this->formatter->getGoodsAmount();
    }

    /**
     * 获取商品积分.
     */
    public function getGoodsTotalIntegral(): int
    {
        return $this->formatter->getGoodsTotalIntegral();
    }
}
