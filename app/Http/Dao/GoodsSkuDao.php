<?php

namespace App\Http\Dao;

use App\Models\GoodsSku;
use App\Models\GoodsSpecValue;
use Illuminate\Support\Collection;

class GoodsSkuDao
{
    /**
     * 后台逆向回显商品规格值规格数据.
     *
     * @param Collection<int,GoodsSku> $collection
     */
    public function reverseData(Collection $collection): array
    {
        if ($collection->isEmpty()) {
            return [];
        }
        $goods_spec_value_ids = array_values(array_unique(explode('|', $collection->pluck('sku_value')->implode('|'))));
        $goods_spec_values = GoodsSpecValue::query()->whereIn('id', $goods_spec_value_ids)->select(['id', 'value', 'goods_spec_id', 'thumb'])->get();

        return $collection->map(function (GoodsSku $goods_sku) use ($goods_spec_values) {
            $info = [
                'id' => $goods_sku->id,
                'price' => $goods_sku->price,
                'thumb' => '',
                'number' => $goods_sku->number,
                'is_show' => $goods_sku->is_show,
                'sort' => $goods_sku->sort,
            ];
            $tmp_sku_value = explode('|', $goods_sku->sku_value);

            foreach ($tmp_sku_value as $key => $value) {
                $tmp_spec_value = $goods_spec_values->where('id', $value)->first();

                if ($key == 0) {
                    $info['thumb'] = $tmp_spec_value->thumb;
                }
                $info['template_'.($key + 1)] = $tmp_spec_value->value;
            }

            return $info;
        })->toArray();
    }
}
