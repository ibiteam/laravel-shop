<?php

namespace App\Http\Resources\Api;

use App\Models\Goods;
use App\Models\GoodsImage;
use App\Models\GoodsParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoodsDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (! $this->resource instanceof Goods) {
            return [];
        }

        return [
            'banner' => [
                'images' => $this->resource->images->map(fn (GoodsImage $goods_image) => $goods_image->image)->unshift($this->resource->image),
                'video' => [
                    'url' => $this->resource->video,
                    'duration' => $this->resource->video_duration,
                ],
            ],
            'center' => [
                'no' => $this->resource->no,
                'name' => $this->resource->name,
                'sub_name' => $this->resource->sub_name,
                'label' => $this->resource->label,
                'price' => $this->resource->price,
                'integral' => $this->resource->integral,
                'integral_name' => $this->resource->integral_name,
                'sales_volume' => $this->resource->is_show_sales_volume ? $this->resource->sales_volume : null,
                'total' => $this->resource->total,
                'status' => $this->resource->status,
                'can_quota' => $this->resource->can_quota,
                'quota_number' => $this->resource->quota_number,
                'unit' => $this->resource->unit,
                'evaluate' => $this->resource->evaluate,
                'parameters' => $this->resource->parameters->map(fn (GoodsParameter $goods_parameter) => ['name' => $goods_parameter->name, 'value' => $goods_parameter->value]),
                'content' => $this->resource->detail?->content,
                'sku_params' => $this->resource->sku_params_list,
            ],
            'bottom' => [
                'cart_number' => $this->resource->cart_number,
                'can_collect' => $this->resource->can_collect,
            ],
        ];
    }
}
