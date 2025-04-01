<?php

namespace App\Services\Goods;

use App\Exceptions\BusinessException;
use App\Models\Goods;
use App\Models\GoodsSku;
use App\Models\GoodsSpecValue;

class ManageSkusService
{
    /**
     * @var array 待删除 Goods Sku Ids
     */
    private $delete_sku_ids = [];

    /**
     * @var array 新增 Goods Sku 数据
     */
    private $insert_sku_data = [];

    /**
     * @var array 待更新 Goods Sku 数据
     */
    private $updated_sku_data = [];

    /**
     * @var array 商品规格值缩略图
     */
    private $spec_value_thumb = [];

    /**
     * @throws BusinessException
     */
    public function __construct(public Goods $goods, array $request_goods_skus_data)
    {
        $this->init($request_goods_skus_data);
    }

    public function getDeleteSkuIds(): array
    {
        return $this->delete_sku_ids;
    }

    public function getInsertSkuData(): array
    {
        return $this->insert_sku_data;
    }

    public function getUpdatedSkuData(): array
    {
        return $this->updated_sku_data;
    }

    public function getSpecValueThumb(): array
    {
        return $this->spec_value_thumb;
    }

    public function exec()
    {
        $tmp_deleted_sku_ids = $this->getDeleteSkuIds();

        if (! empty($tmp_deleted_sku_ids)) {
            $this->goods->skus()->whereIn('id', $tmp_deleted_sku_ids)->delete();
        }

        $tmp_updated_sku_data = $this->getUpdatedSkuData();

        if (! empty($tmp_updated_sku_data)) {
            foreach ($tmp_updated_sku_data as $tmp_datum) {
                $this->goods->skus()->where('id', $tmp_datum['id'])->update($tmp_datum);
            }
        }
        $tmp_insert_sku_data = $this->getInsertSkuData();

        if (! empty($tmp_insert_sku_data)) {
            $this->goods->skus()->createMany($tmp_insert_sku_data);
        }
        $tmp_update_spec_value_thumb = $this->getSpecValueThumb();

        if (! empty($tmp_update_spec_value_thumb)) {
            foreach ($tmp_update_spec_value_thumb as $tmp_thumb_datum) {
                $this->goods->specValues()->where('id', $tmp_thumb_datum['spec_value_id'])->update(['thumb' => $tmp_thumb_datum['thumb']]);
            }
        }
    }

    private function setDeleteSkuIds(array $delete_sku_ids): void
    {
        $this->delete_sku_ids = $delete_sku_ids;
    }

    private function setInsertSkuData(array $insert_sku_data): void
    {
        $this->insert_sku_data[] = $insert_sku_data;
    }

    private function setUpdatedSkuData(array $updated_sku_data): void
    {
        $this->updated_sku_data[] = $updated_sku_data;
    }

    private function setSpecValueThumb(array $spec_value_thumb): void
    {
        $this->spec_value_thumb[] = $spec_value_thumb;
    }

    private function init(array $request_goods_skus_data)
    {
        // 获取 请求中的 sku_id
        $tmp_request_sku_ids = array_filter(array_unique(array_column($request_goods_skus_data, 'id')), function ($goods_sku_id) {
            return (bool) $goods_sku_id;
        });

        if ($tmp_request_sku_ids) {
            $goods_sku_data = GoodsSku::query()->whereGoodsId($this->goods->id)->whereIn('id', $tmp_request_sku_ids)->get();

            if (count($tmp_request_sku_ids) != count($goods_sku_data)) {
                throw new BusinessException('商品规格设置存在问题，请刷新后重试');
            }
        }
        $goods_spec_value_data = GoodsSpecValue::query()->whereGoodsId($this->goods->id)->get();
        $all_goods_sku_ids = GoodsSku::query()->whereGoodsId($this->goods->id)->pluck('id')->toArray();

        // 之前设置过商品 SKU
        if (count($all_goods_sku_ids)) {
            // 取 表中ID与请求ID的差集 即为删除的数据
            $tmp_delete_sku_ids = array_diff($all_goods_sku_ids, $tmp_request_sku_ids);
            $this->setDeleteSkuIds($tmp_delete_sku_ids);
        }
        // 不存在 goods_sku_id 获取 goods_sku_id 为假(0 or null or '')
        $tmp_insert_sku_data = array_filter($request_goods_skus_data, function ($sku_item) {
            return ! isset($sku_item['id']) || ! $sku_item['id'];
        });

        // 整理新增数据
        foreach ($tmp_insert_sku_data as $insert_sku_item) {
            $this->setInsertSkuData($this->skuItemFormat($insert_sku_item, $goods_spec_value_data));
        }
        $tmp_update_sku_data = array_filter($request_goods_skus_data, function ($sku_item) use ($all_goods_sku_ids) {
            return isset($sku_item['id']) && $sku_item['id'] && in_array((int) $sku_item['id'], $all_goods_sku_ids, true);
        });

        // 整理更新数据
        foreach ($tmp_update_sku_data as $update_sku_item) {
            $tmp_update_sku_info = $this->skuItemFormat($update_sku_item, $goods_spec_value_data);
            $tmp_update_sku_info['id'] = $update_sku_item['id'];
            $this->setUpdatedSkuData($tmp_update_sku_info);
        }
    }

    /**
     * @throws BusinessException
     */
    private function skuItemFormat(array $sku_item, $goods_spec_value_data): array
    {
        $sku_value = '';
        $tmp_first_spec_value_name = $sku_item['template_1'] ?? '';
        $tmp_second_spec_value_name = $sku_item['template_2'] ?? '';
        $tmp_third_spec_value_name = $sku_item['template_3'] ?? '';

        // check spec_value_name
        if (! $tmp_first_spec_value_name) {
            throw new BusinessException('未找到一级销售规格相关数据!');
        }

        if (! $tmp_second_spec_value_name && $tmp_third_spec_value_name) {
            throw new BusinessException('未设置二级销售规格数据，暂不支持设置三级销售规格数据');
        }

        // 处理 sku_value and desc
        if ($tmp_first_spec_value_name) {
            $spec_value_name_first_info = $goods_spec_value_data->where('value', $tmp_first_spec_value_name)->first();

            if ($spec_value_name_first_info instanceof GoodsSpecValue) {
                $sku_value .= $spec_value_name_first_info->id.'|';
                // 处理 thumb
                $tmp_spec_thumb = array_filter($this->getSpecValueThumb(), function ($spec_value_thumb) use ($spec_value_name_first_info) {
                    return isset($spec_value_thumb['spec_value_id']) && $spec_value_thumb['spec_value_id'] == $spec_value_name_first_info->id;
                });
                $tmp_spec_first_thumb = [];

                if (! empty($tmp_spec_thumb)) {
                    $tmp_spec_first_thumb = array_values($tmp_spec_thumb)[0];
                }

                if (
                    array_key_exists('thumb', $sku_item)
                    && (empty($tmp_spec_first_thumb) || ! empty($tmp_spec_first_thumb) && $sku_item['thumb'] != $tmp_spec_first_thumb['thumb'])
                    && $sku_item['thumb'] != $spec_value_name_first_info->thumb
                ) {
                    $this->setSpecValueThumb([
                        'spec_value_id' => $spec_value_name_first_info->id,
                        'thumb' => $sku_item['thumb'],
                    ]);
                }
            } else {
                throw new BusinessException('未找到一级销售规格相关数据');
            }
        }

        if ($tmp_second_spec_value_name) {
            $spec_value_name_second_info = $goods_spec_value_data->where('value', $tmp_second_spec_value_name)->first();

            if ($spec_value_name_second_info instanceof GoodsSpecValue) {
                $sku_value .= $spec_value_name_second_info->id.'|';
            } else {
                throw new BusinessException('未找到二级销售规格相关数据');
            }
        }

        // 未设置 二级 数据的情况下 无法设置 三级
        if (! $tmp_second_spec_value_name && $tmp_third_spec_value_name) {
            throw new BusinessException('未设置二级销售规格相关数据，不支持设置三级销售规格相关数据');
        }

        if ($tmp_third_spec_value_name) {
            $spec_value_name_third_info = $goods_spec_value_data->where('value', $tmp_third_spec_value_name)->first();

            if ($spec_value_name_third_info instanceof GoodsSpecValue) {
                $sku_value .= $spec_value_name_third_info->id.'|';
            } else {
                throw new BusinessException('未找到三级销售规格相关数据');
            }
        }

        if (! $sku_value) {
            throw new BusinessException('整理销售规格数据失败，请核对后重试');
        }

        return [
            'sku_value' => rtrim($sku_value, '|'),
            'price' => $sku_item['price'],
            'integral' => $sku_item['integral'] ?? 0,
            'number' => $sku_item['number'],
            'sort' => $sku_item['sort'] ?? 1,
            'is_show' => $sku_item['is_show'] ?? 1,
        ];
    }
}
