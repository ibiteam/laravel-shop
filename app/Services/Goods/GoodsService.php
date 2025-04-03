<?php

namespace App\Services\Goods;

use App\Exceptions\BusinessException;
use App\Exceptions\CustomException;
use App\Http\Dao\CartDao;
use App\Http\Dao\GoodsCollectDao;
use App\Http\Dao\GoodsDao;
use App\Http\Dao\OrderEvaluateDao;
use App\Models\AdminOperationLog;
use App\Models\AdminUser;
use App\Models\Goods;
use App\Models\GoodsCollect;
use App\Models\GoodsSku;
use App\Models\GoodsSpec;
use App\Models\GoodsSpecValue;
use App\Models\ShopConfig;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoodsService
{
    /**
     * @throws BusinessException
     * @throws \Throwable
     */
    public function storeOrUpdate(AdminUser $admin_user, array $params, int $goods_id = 0): void
    {
        $settings_is_open_integral = shop_config(ShopConfig::IS_OPEN_INTEGRAL);

        // 默认数据处理
        $default_data = [
            'category_id' => 0,
            'name' => '',
            'sub_name' => '',
            'label' => '',
            'unit' => '',
            'price' => 0,
            'integral' => 0,
            'total' => 0,
            'type' => 1,
            'status' => 0,
            'can_quota' => 0,
            'quota_number' => '',
            'video' => '',
            'video_duration' => 0,
        ];
        $store_data = array_merge($default_data, array_filter(Arr::only($params, array_keys($default_data)), fn ($value) => ! is_null($value)));

        // 商品图片的处理
        $detail_images = $params['images'];

        $main_image = array_shift($detail_images);
        $detail_images = array_map(fn ($image) => ['image' => $image], $detail_images);

        if (! $main_image) {
            throw new BusinessException('请上传商品主图');
        }
        $store_data = array_merge($store_data, ['image' => $main_image]);

        // 商品参数处理
        $parameters = array_map(function ($item) {
            return ['name' => $item['name'], 'value' => $item['value']];
        }, $params['parameters'] ?? []);

        // 多规格商品校验与处理价格和积分
        $request_sku_data = $params['sku_data'] ?? [];

        if (! empty($request_sku_data)) {
            if ($settings_is_open_integral) {
                $validate_sku_integral = array_filter($request_sku_data, function ($tmp_item) {
                    return $this->checkPriceOrIntegral($tmp_item['price'] ?? 0, $tmp_item['integral'] ?? 0, true);
                });

                if (! empty($validate_sku_integral)) {
                    throw new BusinessException('商品价格与积分不能同时为空~');
                }
            } else {
                $validate_sku_integral = array_filter($request_sku_data, function ($tmp_item) {
                    return $this->checkPriceOrIntegral($tmp_item['price'] ?? 0);
                });

                if (! empty($validate_sku_integral)) {
                    throw new BusinessException('商品价格不能为空！');
                }
            }
            $store_data['price'] = min(array_column($request_sku_data, 'price')) ?? 0;
            $store_data['integral'] = min(array_column($request_sku_data, 'integral')) ?? 0;
        }

        // 校验价格与积分
        if ($settings_is_open_integral) {
            if ($this->checkPriceOrIntegral($store_data['price'], $store_data['integral'], true)) {
                throw new BusinessException('商品价格与积分不能同时为空');
            }
        } else {
            if ($this->checkPriceOrIntegral($store_data['price'])) {
                throw new BusinessException('商品价格不能为空~');
            }
        }

        // 修改商品
        if ($goods_id) {
            $goods = Goods::query()->whereId($goods_id)->first();

            if (! $goods instanceof Goods) {
                throw new BusinessException('商品不存在');
            }

            DB::beginTransaction();

            try {
                /* 商品信息 */
                if (! $goods->update($store_data)) {
                    throw new BusinessException('商品更新失败');
                }

                /* 商品详情内容 */
                if (! $goods->detail()->update(['content' => $params['content']])) {
                    throw new BusinessException('商品详情更新失败');
                }
                /* 商品图片信息 */
                $goods->images()->delete();

                if (! empty($detail_images) && ! $goods->images()->createMany($detail_images)) {
                    throw new BusinessException('商品细节图更新失败');
                }
                /* 产品参数处理 */
                $goods->parameters()->delete();

                if (! empty($parameters) && ! $goods->parameters()->createMany($parameters)) {
                    throw new BusinessException('商品参数更新失败');
                }
                /* 商品 SPEC 处理 */
                (new ManageSpecValueService($goods, $params['spec_data'] ?? []))->exec();

                /* 商品 SKU 处理 */
                (new ManageSkusService($goods, $request_sku_data))->exec();

                admin_operation_log($admin_user, "修改了商品信息:{$goods->goods_sn}[{$goods->id}]", AdminOperationLog::TYPE_UPDATE);
                DB::commit();

                return;
            } catch (BusinessException $business_exception) {
                DB::rollBack();

                throw $business_exception;
            } catch (\Throwable $throwable) {
                DB::rollBack();
                Log::error('修改商品失败:'.$throwable->getMessage(), $throwable->getTrace());

                throw new BusinessException('修改失败');
            }
        }

        // 新增商品
        DB::beginTransaction();

        try {
            $store_data['no'] = Str::uuid()->toString();
            $store_data['sales_volume'] = 0;
            $store_data['sort'] = 0;
            /* 商品信息 */
            $goods = Goods::query()->create($store_data);

            if (! $goods instanceof Goods) {
                throw new BusinessException('商品新增失败');
            }

            /* 商品详情内容 */
            if (! $goods->detail()->create(['content' => $params['content']])) {
                throw new BusinessException('商品详情新增失败');
            }

            /* 商品图片信息 */
            if (! empty($detail_images) && ! $goods->images()->createMany($detail_images)) {
                throw new BusinessException('商品细节图新增失败');
            }

            /* 产品参数处理 */
            if (! empty($parameters) && ! $goods->parameters()->createMany($parameters)) {
                throw new BusinessException('商品参数新增失败');
            }
            /* 商品 SPEC 处理 */
            (new ManageSpecValueService($goods, $params['spec_data'] ?? []))->exec();
            /* 商品 SKU 处理 */
            (new ManageSkusService($goods, $request_sku_data))->exec();

            admin_operation_log($admin_user, "新增了商品信息:{$goods->goods_sn}[{$goods->id}]", AdminOperationLog::TYPE_STORE);
            DB::commit();
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            throw $business_exception;
        } catch (\Throwable $throwable) {
            DB::rollBack();
            Log::error('新增商品失败:'.$throwable->getMessage(), $throwable->getTrace());

            throw new BusinessException('新增失败');
        }
    }

    /**
     * 商品详情.
     *
     * @throws BusinessException
     */
    public function show(string $no, ?User $user, int $request_sku_id = 0): Goods
    {
        $goods = Goods::query()->with(['images', 'parameters', 'detail', 'specValues', 'specValues.spec'])->withTrashed()->whereNo($no)->first();

        if (! $goods instanceof Goods) {
            throw new BusinessException('商品过期不存在');
        }
        app(GoodsDao::class)->checkGoodsIsDestroy($goods);

        // 多规格商品处理
        $tmp_sku_params_list = [
            'sku_item' => null,
            'spec_values' => [],
        ];
        $tmp_sku_data = $goods->skus;
        $tmp_spec_value_data = $goods->specValues;

        if (! empty($tmp_sku_data) && ! empty($tmp_spec_value_data)) {
            $tmp_sku_item = null;
            $tmp_sku_item_format = null;

            if ($request_sku_id > 0) {
                $tmp_sku_item = $tmp_sku_data->where('id', $request_sku_id)->first();
            }

            if (! $tmp_sku_item instanceof GoodsSku) {
                $tmp_sku_item = $tmp_sku_data->first();
            }
            $tmp_selected_spec_value_ids = [];

            if ($tmp_sku_item instanceof GoodsSku) {
                $tmp_sku_item_format = $this->skuItemFormat($tmp_sku_item);
                $tmp_selected_spec_value_ids = explode('|', $tmp_sku_item->sku_value);
            }
            $tmp_sku_params_list = [
                'sku_item' => $tmp_sku_item_format,
                'spec_values' => $this->reverseSpecData($tmp_spec_value_data, $tmp_selected_spec_value_ids),
            ];
        }
        $goods->sku_params_list = $tmp_sku_params_list;

        // 商品评价处理
        [$evaluate_total, $evaluate_list] = app(OrderEvaluateDao::class)->getEvaluateByGoodsId($goods->id);
        $tmp_evaluate = [
            'total' => $evaluate_total,
            'tag_data' => [],
            'items' => $evaluate_list,
        ];

        if ($evaluate_total > 0) {
            $tmp_evaluate['tag_data'] = app(OrderEvaluateDao::class)->getTagListByGoodsId($goods->id);
        }
        $goods->evaluate = $tmp_evaluate;

        // 购物车数量以及是否收藏处理
        if ($user instanceof User) {
            $goods->cart_number = app(CartDao::class)->getValidCarNumber($user->id);

            $tmp_collect = app(GoodsCollectDao::class)->getInfoByUserAndGoodsId($goods->id, $user->id);
            $goods->can_collect = $tmp_collect instanceof GoodsCollect && $tmp_collect->is_attention == GoodsCollect::ATTENTION_YES;
        } else {
            $goods->cart_number = 0;
            $goods->can_collect = false;
        }

        // 积分名称
        $goods->integral_name = shop_config(ShopConfig::INTEGRAL_NAME);
        // 是否展示销量
        $goods->is_show_sales_volume = shop_config(ShopConfig::IS_SHOW_SALES_VOLUME);

        return $goods;
    }

    /**
     * 获取商品SKU信息.
     *
     * @throws BusinessException
     */
    public function getSkuInfoByNo(Goods $goods, string $unique): Collection
    {
        $goods_sku = $goods->skus()->where('sku_value', implode('|', explode('_', $unique)))->first();

        if (! $goods_sku instanceof GoodsSku) {
            throw new BusinessException('商品规格不存在');
        }

        return $this->skuItemFormat($goods_sku);
    }

    /**
     * 实时获取商品库存数量.
     *
     * @param Goods $goods          商品
     * @param int   $request_sku_id SKU id
     * @param int   $number         购买数量
     *
     * @throws BusinessException
     * @throws CustomException
     */
    public function checkGoodsNumber(Goods $goods, int $request_sku_id, int $number): array
    {
        $res = [
            'total' => 0,
            'can_buy' => false,
        ];
        $sku_data = $goods->skus()->get();

        if (! empty($sku_data) && $request_sku_id === 0) {
            throw new BusinessException('多规格商品请先选择商品规格');
        }

        if ($request_sku_id > 0) {
            $sku_item = $sku_data->where('id', $request_sku_id)->first();

            if (! $sku_item instanceof GoodsSku) {
                throw new BusinessException('商品规格不存在');
            }
            $res['total'] = $sku_item->number;

            if ($sku_item->number < $number) {
                $tmp_message = $sku_item->number.$goods->unit;

                throw new customexception("库存数量只有{$tmp_message}，您最多只能购买{$tmp_message}", $res);
            }
            $res['can_buy'] = true;

            return $res;
        }
        $res['total'] = $goods->number;

        if ($goods->number < $number) {
            $tmp_message = $goods->number.$goods->unit;

            throw new customexception("库存数量只有{$tmp_message}，您最多只能购买{$tmp_message}", $res);
        }
        $res['can_buy'] = true;

        return $res;
    }

    /**
     * 检查商品价格和积分是否满足要求.
     */
    private function checkPriceOrIntegral(mixed $price, mixed $integral = null, bool $is_check_integral = false): bool
    {
        if ($is_check_integral) {
            return (! is_numeric($price) || $price <= 0) && (! is_numeric($integral) || $integral <= 0);
        }

        return ! is_numeric($price) || $price <= 0;
    }

    /**
     * 商品规格格式化.
     */
    private function skuItemFormat(GoodsSku $goods_sku): Collection
    {
        return collect([
            'id' => $goods_sku->id,
            'unique' => implode('_', explode('|', $goods_sku->sku_value)),
            'price' => $goods_sku->price,
            'integral' => $goods_sku->integral,
            'number' => $goods_sku->number,
            'has_number' => $goods_sku->number > 0,
        ]);
    }

    /**
     * @param Collection<int,GoodsSpecValue> $collection
     */
    private function reverseSpecData(Collection $collection, array $selected_ids = []): EloquentCollection|Collection
    {
        $group_collection = $collection->groupBy('goods_spec_id');

        return GoodsSpec::query()->whereIn('id', $group_collection->keys())->get()->map(function (GoodsSpec $goods_spec) use ($group_collection, $selected_ids) {
            return [
                'id' => $goods_spec->id,
                'name' => $goods_spec->name,
                'values' => $group_collection->get($goods_spec->id)->map(function (GoodsSpecValue $goods_spec_value) use ($selected_ids) {
                    return [
                        'id' => $goods_spec_value->id,
                        'name' => $goods_spec_value->value,
                        'thumb' => $goods_spec_value->thumb,
                        'selected' => in_array($goods_spec_value->id, $selected_ids),
                    ];
                })->values(),
            ];
        });
    }
}
