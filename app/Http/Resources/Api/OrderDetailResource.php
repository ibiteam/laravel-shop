<?php

namespace App\Http\Resources\Api;

use App\Http\Dao\ApplyRefundDao;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            'status' => $this->resource->custom_status,
            'order' => [
                'order_sn' => $this->resource->order_sn,
                'paid_at' => $this->resource->paid_at ? $this->resource->paid_at->format('Y-m-d H:i:s') : '',
                'remark' => $this->resource->remark,
                'status' => $this->resource->custom_status,
            ],
            'logistics' => $this->resource->order_delivery_count > 0 ? [
                'title' => '运输中',
                'description' => '当前订单已拆分成'.$this->resource->order_delivery_count.'个运单运输，点击可以查看物流轨迹',
                'shipped_at' => $this->resource->shipped_at,
                'delivery_no' => $this->resource->orderDelivery->first()?->delivery_no,
            ] : null,
            'logistics_number' => $this->resource->order_delivery_count,
            'address' => [
                'consignee' => $this->resource->consignee,
                'address' => $this->resource->province?->name.$this->resource->city?->name.$this->resource->district?->name.$this->resource->address,
                'phone' => phone_hidden($this->resource->phone),
            ],
            'items' => $this->resource->detail->map(function (OrderDetail $item) {
                return [
                    'id' => $item->id,
                    'goods_no' => $item->goods_no,
                    'goods_name' => $item->goods_name,
                    'goods_unit' => $item->goods_unit,
                    'goods_image' => $item->goods?->image,
                    'goods_price' => price_number_format($item->goods_price),
                    'number' => $item->goods_number,
                    'sku_value' => $item->skuValue(),
                    'sku_id' => $item->goods_sku_id,
                    'refund_action' => app(ApplyRefundDao::class)->showAfterSales($this->resource, $item),
                ];
            }),
            'amounts' => [
                'goods_amount' => price_number_format($this->resource->goods_amount),
                'money_paid' => price_number_format($this->resource->money_paid),
                'shipping_fee' => price_number_format($this->resource->shipping_fee),
                'coupon_amount' => price_number_format($this->resource->coupon_amount),
                'integral' => price_number_format($this->resource->integral),
                'order_amount' => price_number_format($this->resource->order_amount),
            ],
        ];
    }
}
