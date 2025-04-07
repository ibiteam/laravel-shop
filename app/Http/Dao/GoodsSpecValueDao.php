<?php

namespace App\Http\Dao;

use App\Models\GoodsSpec;
use App\Models\GoodsSpecValue;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class GoodsSpecValueDao
{
    /**
     * 后台逆向回显商品规格值规格数据.
     *
     * @param Collection<int,GoodsSpecValue> $collection
     */
    public function reverseData(Collection $collection): array
    {
        if ($collection->isEmpty()) {
            return [];
        }
        $goods_spec_ids = $collection->pluck('goods_spec_id')->toArray();
        $goods_spec_values = $collection->map(fn (GoodsSpecValue $goods_spec_value) => [
            'id' => $goods_spec_value->id,
            'goods_spec_id' => $goods_spec_value->goods_spec_id,
            'name' => $goods_spec_value->value,
            'thumb' => $goods_spec_value->thumb,
        ])->groupBy('goods_spec_id');

        return GoodsSpec::query()->whereIn('id', $goods_spec_ids)->select(['id', 'name'])->get()->map(function (GoodsSpec $goods_spec) use ($goods_spec_values) {
            return [
                'id' => $goods_spec->id,
                'name' => $goods_spec->name,
                'values' => $goods_spec_values[$goods_spec->id] ?? [],
            ];
        })->toArray();
    }

    public function getInfoByIds(array $spec_value_ids, int $goods_id): EloquentCollection|Collection
    {
        return GoodsSpecValue::query()->with('spec')->whereIn('id', $spec_value_ids)->where('goods_id', $goods_id)->get();
    }
}
