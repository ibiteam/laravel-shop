<?php

namespace App\Http\Resources\Api;

use App\Models\ApplyRefund;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplyRefundResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (! $this->resource instanceof ApplyRefund) {
            return [];
        }

        $user = $this->resource->user;
        $order = $this->resource->order;
        $orderDetail = $this->resource->orderDetail;

        return [
            'id' => $this->resource->id,
            'no' => $this->resource->no,
            'money' => $this->resource->money,
            'number' => $this->resource->number,
            'type' => $this->resource->type,
            'status' => $this->resource->status,
            'created_at' => $this->resource->created_at->toDateTimeString(),
            'deal_end_time' => strtotime($this->resource->job_time), // 计算待处理结束时间戳（未发货申请响应时间 已发货申请响应时间）
            'now_time' => time(),
            'order_no' => $order?->no,
            'order_detail_id' => $orderDetail?->id,
            'goods_no' => $orderDetail?->goods_no,
            'goods_name' => $orderDetail?->goods_name,
            'goods_image' => $orderDetail?->goods->image,
            'goods_sku_id' => $orderDetail?->goods_sku_id,
            'goods_sku_value' => $orderDetail?->goods_sku_value,
        ];
    }
}
