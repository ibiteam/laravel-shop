<?php

namespace App\Http\Resources\Manage;

use App\Enums\OrderStatusEnum;
use App\Enums\PayStatusEnum;
use App\Enums\RefererEnum;
use App\Enums\ShippingStatusEnum;
use App\Models\Order;
use App\Models\ShopConfig;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'id' => $this->resource->id,
            'order_sn' => $this->resource->order_sn,
            'payer' => [
                'user_name' => $this->resource->user?->user_name,
                'done_time' => $this->resource->created_at->format('Y-m-d H:i:s'),
            ],
            'consignee' => $this->resource->consignee,
            'order_amount' => $this->resource->order_amount,
            'integral' => $this->resource->integral,
            'integral_name' => shop_config(ShopConfig::INTEGRAL_NAME),
            'money_paid' => $this->resource->money_paid,
            'source' => RefererEnum::formSource($this->resource->source)->getLabel(),
            'status' => implode(',', [
                OrderStatusEnum::formSource($this->resource->order_status)->getLabel(),
                PayStatusEnum::formSource($this->resource->pay_status)->getLabel(),
                ShippingStatusEnum::formSource($this->resource->ship_status)->getLabel(),
            ]),
        ];
    }
}
