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

    public function __construct(public Goods $goods, array $params)
    {
        $this->init($params);
    }

    public function getStoreSpecValueData(): array
    {
        return array_values($this->store_spec_value_data);
    }

    public function setStoreSpecValueData(array $store_spec_value_data): void
    {
        $this->store_spec_value_data[$store_spec_value_data['goods_spec_id'].$store_spec_value_data['value']] = $store_spec_value_data;
    }

    public function getUpdatedSpecValueData(): array
    {
        return array_values($this->updated_spec_value_data);
    }

    public function setUpdatedSpecValueData(array $updated_spec_value_data): void
    {
        $this->updated_spec_value_data[$updated_spec_value_data['id']] = $updated_spec_value_data;
    }

    public function getDeletedSpecValueData(): array
    {
        return $this->deleted_spec_value_data;
    }

    public function setDeletedSpecValueData(array $deleted_spec_value_data): void
    {
        $this->deleted_spec_value_data = $deleted_spec_value_data;
    }

    public function exec(): void
    {
        // 删除
        if ($deleted_ids = $this->getDeletedSpecValueData()) {
            $this->goods->specValues()->whereIn('id', $deleted_ids)->delete();
        }

        // 新增
        if ($store_data = $this->getStoreSpecValueData()) {
            $this->goods->specValues()->createMany($store_data);
        }

        // 更新
        foreach ($this->getUpdatedSpecValueData() as $updated_spec_value_datum) {
            $spec_value = $this->goods->specValues()->where('id', $updated_spec_value_datum['id'])->first();

            if ($spec_value instanceof GoodsSpecValue) {
                $spec_value->update($updated_spec_value_datum);
            }
        }
    }

    private function init(array $params): void
    {
        $goods_specs = GoodsSpec::query()->get();
        $exists_spec_value_ids = $this->goods->specValues()->pluck('id')->toArray();

        $tmp_spec_values = collect();

        foreach ($params as $param) {
            // 商品规格处理
            if (! isset($param['id']) || ! $param['id']) {
                $goods_spec = $goods_specs->where('name', $param['name'])->first();

                if (! $goods_spec instanceof GoodsSpec) {
                    $goods_spec = GoodsSpec::query()->create(['name' => $param['name'], 'value' => array_column($param['values'], 'name'), 'is_show' => GoodsSpec::SHOW]);
                }
            } else {
                $goods_spec = $goods_specs->where('id', $param['id'])->first();

                if (! $goods_spec instanceof GoodsSpec) {
                    throw new BusinessException('获取商品规格失败');
                }

                // 不同的规格名，需要重新创建
                if ($goods_spec->name !== $param['name']) {
                    $goods_spec = GoodsSpec::query()->create(['name' => $param['name'], 'value' => array_column($param['values'], 'name'), 'is_show' => GoodsSpec::SHOW]);
                }
            }

            // 商品规格值处理
            foreach ($param['values'] as $key => $value) {
                $tmp_spec_values->push(['id' => $value['id'] ?? 0, 'goods_spec_id' => $goods_spec->id, 'value' => $value['name'], 'thumb' => '', 'sort' => $key]);
            }
            $tmp_spec_values->each(function ($item) use ($exists_spec_value_ids) {
                if (in_array($item['id'], $exists_spec_value_ids)) {
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
