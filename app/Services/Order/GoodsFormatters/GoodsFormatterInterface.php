<?php

namespace App\Services\Order\GoodsFormatters;

interface GoodsFormatterInterface
{
    /**
     * 验证商品信息.
     */
    public function validate(): self;

    /**
     * 结算页商品信息格式化.
     */
    public function settlementFormat(): array;

    /**
     * 订单商品信息格式化.
     */
    public function buildOrderItem(): array;

    /**
     * 减少库存.
     *
     * @param bool $payed 是否支付
     */
    public function decrementStock(bool $payed = false): void;
}
