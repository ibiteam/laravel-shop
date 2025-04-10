<?php

namespace App\Http\Dao;

use App\Http\Resources\ApiOrderEvaluateResource;
use App\Models\OrderEvaluate;
use App\Models\OrderEvaluateCount;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderEvaluateDao
{
    /**
     * 根据商品ID获取评价列表(分页).
     */
    public function getListByGoodsId(int $goods_id, int $page = 1, int $number = 10): LengthAwarePaginator
    {
        return OrderEvaluate::query()
            ->with('user:id,nickname,avatar')
            ->whereGoodsId($goods_id)
            ->whereStatus(OrderEvaluate::STATUS_SUCCESS)
            ->orderBy('created_at', 'desc')
            ->take($number)
            ->paginate($number, page: $page);
    }

    /**
     * 商品详情-获取商品评价.
     */
    public function getEvaluateByGoodsId(int $goods_id, int $number = 2): array
    {
        $order_evaluate = $this->getListByGoodsId($goods_id, number: $number);
        $total = $order_evaluate->total();
        $list = $order_evaluate->getCollection()->map(fn (OrderEvaluate $order_evaluate) => ApiOrderEvaluateResource::make($order_evaluate));

        return [$total, $list];
    }

    public function getTagListByGoodsId(int $goods_id): array
    {
        $data = OrderEvaluateCount::query()->whereGoodsId($goods_id)->select(['rank_total', 'goods_rank_total', 'price_rank_total'])->first();

        $tags = [];

        foreach ($data->attributesToArray() as $key => $value) {
            if (! isset($data->tags[$key]) || ! $value) {
                continue;
            }
            $tags[] = [
                'name' => $data->tags[$key],
                'value' => chinese_number_down_format($value),
                'type' => $key,
            ];
        }

        return $tags;
    }
}
