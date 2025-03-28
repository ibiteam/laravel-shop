<?php

namespace App\Services\Goods;

use App\Exceptions\BusinessException;
use App\Models\Goods;
use App\Models\GoodsSpec;
use App\Models\GoodsSpecValue;

class GoodsSpecValueService
{
    private array $store_spec_value_data = [];

    private array $updated_spec_value_data = [];

    private array $deleted_spec_value_data = [];

    public function __construct(array $params, Goods $goods)
    {
        $this->init($params, $goods);
    }

    public function getStoreSpecValueData(): array
    {
        return $this->store_spec_value_data;
    }

    public function setStoreSpecValueData(array $store_spec_value_data): void
    {
        $this->store_spec_value_data[] = $store_spec_value_data;
    }

    public function getUpdatedSpecValueData(): array
    {
        return $this->updated_spec_value_data;
    }

    public function setUpdatedSpecValueData(array $updated_spec_value_data): void
    {
        $this->updated_spec_value_data[] = $updated_spec_value_data;
    }

    public function getDeletedSpecValueData(): array
    {
        return $this->deleted_spec_value_data;
    }

    public function setDeletedSpecValueData(array $deleted_spec_value_data): void
    {
        $this->deleted_spec_value_data[] = $deleted_spec_value_data;
    }

    public function exec(Goods $goods): void
    {
        // 删除
        if ($deleted_ids = $this->getDeletedSpecValueData()) {
            $goods->specValues()->whereIn('id', $deleted_ids)->delete();
        }

        // 新增
        if ($store_data = $this->getStoreSpecValueData()) {
            $goods->specValues()->createMany($store_data);
        }

        // 更新
        foreach ($this->getUpdatedSpecValueData() as $updated_spec_value_datum) {
            $spec_value = $goods->specValues()->where('id', $updated_spec_value_datum['id'])->first();

            if ($spec_value instanceof GoodsSpecValue) {
                $spec_value->update($updated_spec_value_datum);
            }
        }
    }

    private function init(array $params, ?Goods $goods = null): void
    {
        $goods_specs = GoodsSpec::query()->get();
        $exists_spec_value_ids = $goods instanceof Goods ? $goods->specValues()->pluck('id')->toArray() : [];

        $tmp_spec_values = collect();

        foreach ($params as $param) {
            // 商品规格处理
            if (! isset($param['id']) || ! $param['id']) {
                $goods_spec = $goods_specs->where('name', $param['name'])->first();

                if (! $goods_spec instanceof GoodsSpec) {
                    $goods_spec = GoodsSpec::query()->create(['name' => $param['name'], 'value' => array_column($params['values'], 'name'), 'is_show' => GoodsSpec::SHOW]);
                }
            } else {
                $goods_spec = $goods_specs->where('id', $param['id'])->first();

                if (! $goods_spec instanceof GoodsSpec) {
                    throw new BusinessException('获取商品规格失败');
                }

                // 不同的规格名，需要重新创建
                if ($goods_spec->name !== $param['name']) {
                    $goods_spec = GoodsSpec::query()->create(['name' => $param['name'], 'value' => array_column($params['values'], 'name'), 'is_show' => GoodsSpec::SHOW]);
                }
            }

            // 商品规格值处理
            foreach ($param['values'] as $key => $value) {
                $tmp_spec_values->push(['id' => $value['id'] ?? 0, 'goods_spec_id' => $goods_spec->id, 'value' => $value['name'], 'thumb' => '', 'sort' => $key]);
            }
            $tmp_spec_values->each(function ($item) use ($exists_spec_value_ids) {
                if (in_array($item['id'], $exists_spec_value_ids) === 0) {
                    $this->setUpdatedSpecValueData($item);
                } else {
                    unset($item['id']);
                    $this->setStoreSpecValueData($item);
                }
            });
        }

        $temp_update_spec_value_ids = array_column($this->getUpdatedSpecValueData(), 'id');
        $this->setDeletedSpecValueData(array_diff($exists_spec_value_ids, $temp_update_spec_value_ids));
    }
}
