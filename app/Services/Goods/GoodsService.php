<?php

namespace App\Services\Goods;

use App\Exceptions\BusinessException;
use App\Models\AdminOperationLog;
use App\Models\AdminUser;
use App\Models\Goods;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoodsService
{
    public function storeOrUpdate(AdminUser $admin_user, array $params, int $goods_id = 0): void
    {
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
        $store_data = array_merge($default_data, Arr::only($params, array_keys($default_data)));

        // 商品图片的处理
        $detail_images = $params['images'];

        $main_image = array_shift($detail_images);

        if (! $main_image) {
            throw new BusinessException('请上传商品主图');
        }
        $store_data = array_merge($store_data, ['image' => $main_image]);

        $parameters = array_map(function ($item) {
            return ['name' => $item['name'], 'value' => $item['value']];
        }, $params['parameters'] ?? []);

        if ($goods_id) {
            // 修改
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
                (new ManageSkusService($goods, $params['sku_data'] ?? []))->exec();

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

        // 新增

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
            (new ManageSkusService($goods, $params['sku_data'] ?? []))->exec();

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
}
