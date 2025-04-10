<?php

namespace App\Http\Resources;

use App\Models\OrderEvaluate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiOrderEvaluateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (! $this->resource instanceof OrderEvaluate) {
            return [];
        }

        $user = $this->resource->user;

        return [
            'id' => $this->resource->id,
            'nickname' => $this->resource->is_anonymous ? '匿名用户' : $user?->nickname,
            'avatar' => $user?->avatar,
            'content' => $this->resource->comment,
            'images' => $this->resource->images,
            'rank' => $this->resource->rank,
            'goods_rank' => $this->resource->goods_rank,
            'price_rank' => $this->resource->price_rank,
            'bus_rank' => $this->resource->bus_rank,
            'delivery_rank' => $this->resource->delivery_rank,
            'service_rank' => $this->resource->service_rank,
            'comment_at' => $this->resource->comment_at,
        ];
    }
}
