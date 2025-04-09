<?php

namespace App\Http\Resources;

use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\ShippingStatusEnum;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderDetail;
use App\Models\ShipCompany;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiOrderResource extends JsonResource
{
    private const STATUS_NOT_CONFIRM = 1; // 待确认
    private const STATUS_CANCELLED = 2; // 已取消
    private const STATUS_WAIT_PAY = 3; // 待付款
    private const STATUS_WAIT_SHIP = 4; // 待发货
    private const STATUS_WAIT_RECEIVE = 5; // 待收货
    private const STATUS_SUCCESS = 6; // 已完成

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
        $status = $this->getStatus();
        $last_logistics = $this->getLastLogistics();
        $default_evaluate = $this->getEvaluate();

        return [
            'no' => $this->resource->no,
            'status' => $status,
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
            'order_amount' => price_number_format($this->resource->order_amount),
            'logistics' => $last_logistics,
            'evaluate' => $default_evaluate,
            'buttons' => $this->getButtons(
                $status,
                is_array($last_logistics),
                is_array($default_evaluate)
            ),
        ];
    }

    /**
     * 用于显示订单状态
     */
    private function getStatus(): int
    {
        if ($this->resource->order_status === OrderStatusEnum::UNCONFIRMED->value) {
            return self::STATUS_NOT_CONFIRM;
        }

        if ($this->resource->order_status === OrderStatusEnum::CANCELLED->value) {
            return self::STATUS_CANCELLED;
        }

        if ($this->resource->pay_status === PayStatusEnum::PAY_WAIT->value) {
            return self::STATUS_WAIT_PAY;
        }

        if ($this->resource->ship_status === ShippingStatusEnum::UNSHIPPED->value) {
            return self::STATUS_WAIT_SHIP;
        }

        if ($this->resource->ship_status === ShippingStatusEnum::SHIPPED->value) {
            return self::STATUS_WAIT_RECEIVE;
        }

        return self::STATUS_SUCCESS;
    }

    /**
     * @param int  $status        页面状态
     * @param bool $has_logistics 是否有物流
     * @param bool $not_evaluate  是否未评价
     */
    private function getButtons(int $status, bool $has_logistics = false, bool $not_evaluate = false): array
    {
        if (! $this->resource instanceof Order) {
            return [];
        }
        $buttons = [];

        switch ($status) {
            case self::STATUS_NOT_CONFIRM:
                $buttons[] = ['text' => '取消订单', 'action' => 'cancel'];

                break;

            case self::STATUS_CANCELLED:
                $buttons[] = ['text' => '删除订单', 'action' => 'delete'];

                if ($this->resource->detail_count === 1) {
                    $buttons[] = ['text' => '再次购买', 'action' => 'again'];
                }

                break;

            case self::STATUS_WAIT_PAY:
                $buttons[] = ['text' => '取消订单', 'action' => 'cancel'];

                if (! $this->resource->is_edit_address) {
                    $buttons[] = ['text' => '修改地址', 'action' => 'edit_address'];
                }

                $buttons[] = ['text' => '去支付', 'action' => 'pay'];

                break;

            case self::STATUS_WAIT_SHIP:
                $buttons[] = ['text' => '申请售后', 'action' => 'refund'];

                if (! $this->resource->is_edit_address) {
                    $buttons[] = ['text' => '修改地址', 'action' => 'edit_address'];
                }

                break;

            case self::STATUS_WAIT_RECEIVE:
                if ($has_logistics) {
                    $buttons[] = ['text' => '查看物流', 'action' => 'logistics'];
                }

                if ($this->resource->detail_count === 1) {
                    $buttons[] = ['text' => '再次购买', 'action' => 'again'];
                }
                $buttons[] = ['text' => '确认收货', 'action' => 'receive'];

                break;

            case self::STATUS_SUCCESS:
                $buttons[] = ['text' => '删除订单', 'action' => 'delete'];

                if ($not_evaluate) {
                    $buttons[] = ['text' => '去评价', 'action' => 'evaluate'];
                }

                if ($this->resource->detail_count === 1) {
                    $buttons[] = ['text' => '再次购买', 'action' => 'again'];
                }

                break;
        }

        return $buttons;
    }

    private function getLastLogistics(): ?array
    {
        // 订单非已确认已付款已发货状态下直接返回
        if (! (
            $this->resource->order_status === OrderStatusEnum::CONFIRMED->value
            && $this->resource->pay_status === PayStatusEnum::PAYED->value
            && $this->resource->ship_status === ShippingStatusEnum::SHIPPED->value
        )) {
            return null;
        }

        $last_order_delivery = $this->resource->orderDelivery->sortByDesc('id')->first();

        if ($last_order_delivery instanceof OrderDelivery) {
            $ship_company = $last_order_delivery->shipCompany;

            if ($ship_company instanceof ShipCompany) {
                return [
                    'title' => '已发货',
                    'description' => "您的订单将交付{$ship_company->name}，运单号：{$last_order_delivery->ship_no}",
                    'shipped_at' => $last_order_delivery->shipped_at,
                ];
            }
        }

        return null;
    }

    private function getEvaluate(): ?array
    {
        if (! (
            $this->resource->order_status === OrderStatusEnum::CONFIRMED->value
            && $this->resource->pay_status === PayStatusEnum::PAYED->value
            && $this->resource->ship_status === ShippingStatusEnum::RECEIVED->value
        )) {
            return null;
        }

        if ($this->resource->evaluate->isNotEmpty()) {
            return null;
        }

        return [
            'default_value' => 4,
            'description' => '请对订单进行评价',
        ];
    }
}
