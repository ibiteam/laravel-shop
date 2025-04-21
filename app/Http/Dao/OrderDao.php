<?php

namespace App\Http\Dao;

use App\Enums\OrderConstantEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderDetail;

class OrderDao
{
    public function getInfoByOrderSnAndUserId(string $order_sn, int $user_id): ?Order
    {
        return Order::query()->whereUserId($user_id)->whereOrderSn($order_sn)->first();
    }

    /**
     * 获取订单状态
     */
    public function getStatusByOrder(Order $order): OrderConstantEnum
    {
        if ($order->order_status === OrderStatusEnum::UNCONFIRMED->value) {
            return OrderConstantEnum::STATUS_NOT_CONFIRM;
        }

        if ($order->order_status === OrderStatusEnum::CANCELLED->value) {
            return OrderConstantEnum::STATUS_CANCELLED;
        }

        if ($order->pay_status === PayStatusEnum::PAY_WAIT->value) {
            return OrderConstantEnum::STATUS_WAIT_PAY;
        }

        if ($order->ship_status === ShippingStatusEnum::UNSHIPPED->value) {
            return OrderConstantEnum::STATUS_WAIT_SHIP;
        }

        if ($order->ship_status === ShippingStatusEnum::PART->value) {
            return OrderConstantEnum::STATUS_PART;
        }

        if ($order->ship_status === ShippingStatusEnum::SHIPPED->value) {
            return OrderConstantEnum::STATUS_WAIT_RECEIVE;
        }

        return OrderConstantEnum::STATUS_SUCCESS;
    }

    /**
     * 是否允许评价.
     */
    public function canReceive(Order $order, ?OrderConstantEnum $order_constant_enum = null): bool
    {
        if (is_null($order_constant_enum)) {
            $order_constant_enum = $this->getStatusByOrder($order);
        }

        if ($order_constant_enum !== OrderConstantEnum::STATUS_WAIT_RECEIVE) {
            return false;
        }

        if ($order->orderDelivery->isEmpty() || $order->orderDelivery->where('status', OrderDelivery::STATUS_WAIT)->isEmpty()) {
            return false;
        }

        return true;
    }

    /**
     * 是否允许评价.
     */
    public function canEvaluate(Order $order, ?OrderConstantEnum $order_constant_enum = null): bool
    {
        if (is_null($order_constant_enum)) {
            $order_constant_enum = $this->getStatusByOrder($order);
        }

        if ($order_constant_enum !== OrderConstantEnum::STATUS_SUCCESS) {
            return false;
        }

        if ($order->evaluate->isNotEmpty()) {
            return false;
        }

        return true;
    }

    /**
     * 是否允许删除.
     */
    public function canDestroy(Order $order): bool
    {
        $order_constant_enum = $this->getStatusByOrder($order);

        if (in_array($order_constant_enum, [OrderConstantEnum::STATUS_CANCELLED, OrderConstantEnum::STATUS_SUCCESS])) {
            return true;
        }

        return false;
    }

    /**
     * 是否允许取消.
     */
    public function canCancel(Order $order): bool
    {
        $order_constant_enum = $this->getStatusByOrder($order);

        if (in_array($order_constant_enum, [OrderConstantEnum::STATUS_NOT_CONFIRM, OrderConstantEnum::STATUS_WAIT_PAY])) {
            return true;
        }

        return false;
    }

    /**
     * 是否允许修改地址
     */
    public function canEditAddress(Order $order, bool $is_can_status = false): bool
    {
        if ($order->is_edit_address) {
            return false;
        }

        if ($is_can_status) {
            $order_constant_enum = $this->getStatusByOrder($order);

            if (! in_array($order_constant_enum, [OrderConstantEnum::STATUS_WAIT_PAY, OrderConstantEnum::STATUS_WAIT_SHIP])) {
                return false;
            }
        }

        return true;
    }
}
