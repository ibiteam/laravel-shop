<?php

namespace App\Http\Resources;

use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderEvaluate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (! $this->resource instanceof Order) {
            return [];
        }

        return [
            'no' => $this->resource->no,
            'status' => $this->getStatus(),
            'order_amount' => price_number_format($this->resource->order_amount),
            'can_edit_user_address' => $this->getCanEditUserAddress(),
            'can_cancel' => $this->getCanCancel(),
            'can_pay' => $this->getCanPay(),
            'can_logistics' => $this->getCanLogistics(),
            'can_receive' => $this->getCanReceive(),
            'can_refund' => $this->getCanRefund(),
            'can_delete' => $this->getCanDelete(),
            'can_comment' => $this->getCanComment(),
            'can_buy_again' => $this->getCanBuyAgain(),
            'items' => $this->resource->detail->map(function (OrderDetail $item) {
                return [
                    'goods_no' => $item->goods_no,
                    'goods_name' => $item->goods_name,
                    'goods_image' => $item->goods?->image,
                    'goods_price' => price_number_format($item->goods_price),
                    'number' => $item->goods_number,
                    'sku_value' => $item->goods_sku_value,
                    'sku_id' => $item->goods_sku_id,
                ];
            }),
            'logistics' => [],
        ];
    }

    private function getStatus(): string
    {
        if ($this->resource->order_status === OrderStatusEnum::UNCONFIRMED->value) {
            return '未确认';
        }

        if ($this->resource->order_status === OrderStatusEnum::CANCELLED->value) {
            return '已取消';
        }

        if ($this->resource->pay_status === PayStatusEnum::PAY_WAIT->value) {
            return '待付款';
        }

        if ($this->resource->ship_status === ShippingStatusEnum::UNSHIPPED->value) {
            return '待发货';
        }

        if ($this->resource->ship_status === ShippingStatusEnum::SHIPPED->value) {
            return '待收货';
        }

        return '已完成';
    }

    private function getCanCancel(): bool
    {
        // 待确认
        if ($this->resource->order_status === OrderStatusEnum::UNCONFIRMED->value) {
            return true;
        }

        // 已确认 待付款
        if ($this->resource->order_status === OrderStatusEnum::CONFIRMED->value && $this->resource->pay_status === PayStatusEnum::PAY_WAIT->value) {
            return true;
        }

        return false;
    }

    private function getCanPay(): bool
    {
        if ($this->resource->order_status === OrderStatusEnum::CONFIRMED->value && $this->resource->pay_status === PayStatusEnum::PAY_WAIT->value) {
            return true;
        }

        return false;
    }

    private function getCanLogistics(): bool
    {
        if (
            $this->resource->order_status === OrderStatusEnum::CONFIRMED->value
            && $this->resource->pay_status === PayStatusEnum::PAYED->value
            && $this->resource->ship_status === ShippingStatusEnum::SHIPPED->value
        ) {
            return true;
        }

        return false;
    }

    private function getCanReceive(): bool
    {
        if (
            $this->resource->order_status === OrderStatusEnum::CONFIRMED->value
            && $this->resource->pay_status === PayStatusEnum::PAYED->value
            && $this->resource->ship_status === ShippingStatusEnum::SHIPPED->value
        ) {
            return true;
        }

        return false;
    }

    private function getCanRefund(): bool
    {
        return false;
    }

    private function getCanDelete(): bool
    {
        // 已取消 或 已确认已付款已收货
        if (
            $this->resource->order_status === OrderStatusEnum::CANCELLED->value
            || $this->resource->order_status === OrderStatusEnum::CONFIRMED->value
            && $this->resource->pay_status === PayStatusEnum::PAYED->value
            && $this->resource->ship_status === ShippingStatusEnum::RECEIVED->value
        ) {
            return true;
        }

        return false;
    }

    private function getCanComment(): bool
    {
        if (
            $this->resource->order_status !== OrderStatusEnum::CONFIRMED->value
            || $this->resource->pay_status !== PayStatusEnum::PAYED->value
            || $this->resource->ship_status !== ShippingStatusEnum::RECEIVED->value
        ) {
            return false;
        }

        if (OrderEvaluate::query()->whereOrderId($this->resource->id)->exists()) {
            return false;
        }

        return true;
    }

    private function getCanBuyAgain(): bool
    {
        if (
            $this->resource->order_status === OrderStatusEnum::CANCELLED->value
            || $this->resource->order_status === OrderStatusEnum::CONFIRMED->value
            && $this->resource->pay_status === PayStatusEnum::PAYED->value
            && $this->resource->ship_status === ShippingStatusEnum::RECEIVED->value
        ) {
            return true;
        }

        return false;
    }

    private function getCanEditUserAddress(): bool
    {
        // 未修改过收货地址 && 订单状态 !== 已取消 && 发货状态 === 未发货
        if (
            ! $this->resource->is_edit_address
            && $this->resource->order_status !== OrderStatusEnum::CANCELLED->value
            && $this->resource->ship_status === ShippingStatusEnum::UNSHIPPED->value
        ) {
            return true;
        }

        return false;
    }
}
