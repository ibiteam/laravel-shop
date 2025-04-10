<?php

namespace App\Http\Dao;

use App\Enums\OrderConstantEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;
use App\Models\Order;
use App\Models\OrderDetail;

class OrderDao
{
    public function getInfoByNo(string $no, int $user_id): ?Order
    {
        return Order::query()->whereUserId($user_id)->whereNo($no)->first();
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

        if ($order->ship_status === ShippingStatusEnum::SHIPPED->value) {
            return OrderConstantEnum::STATUS_WAIT_RECEIVE;
        }

        return OrderConstantEnum::STATUS_SUCCESS;
    }

    /**
     * 是否允许删除.
     */
    public function canDestroy(Order $order): bool
    {
        $status = $this->getStatusByOrder($order);

        if (in_array($status, [OrderConstantEnum::STATUS_CANCELLED, OrderConstantEnum::STATUS_SUCCESS])) {
            return true;
        }

        return false;
    }

    /**
     * 是否允许删除.
     */
    public function canCancel(Order $order): bool
    {
        $status = $this->getStatusByOrder($order);

        if (in_array($status, [OrderConstantEnum::STATUS_NOT_CONFIRM, OrderConstantEnum::STATUS_WAIT_PAY])) {
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
            if (! in_array($this->getStatusByOrder($order), [OrderConstantEnum::STATUS_WAIT_PAY, OrderConstantEnum::STATUS_WAIT_SHIP])) {
                return false;
            }
        }

        return true;
    }

    public function refundActionByOrderDetail(OrderDetail $order_detail): int
    {
        // todo operate: 订单详情申请售后按钮状态
        return 0;
    }
}
