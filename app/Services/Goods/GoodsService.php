<?php

namespace App\Services\Goods;

use App\Exceptions\BusinessException;
use App\Models\AdminOperationLog;
use App\Models\AdminUser;
use App\Models\Goods;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoodsService
{
    public function storeOrUpdate(AdminUser $admin_user, array $params, string $goods_sn = ''): void
    {
        $sku_data = [];
        $store_data = Arr::only($params, ['category_id', 'seller_category_id', 'brand_id', 'goods_name', 'goods_sub_name', 'keywords', 'buy_min_number', 'video', 'video_duration']);

        if (! isset($params['goods_skus']) || empty($params['goods_skus'])) {
            $store_data = array_merge($store_data, ['price' => $params['price'], 'number' => $params['number']]);
        } else {
            $sku_data = $params['goods_skus'];
        }
        // 商品图片的处理
        $main_image = null;
        $detail_images = [];

        foreach ($params['images'] as $image) {
            $original_image = $image['url'];

            if ($image['type']) {
                $main_image = $original_image;
            } else {
                $detail_images[] = [
                    'goods_original' => $original_image,
                    'goods_thumb' => $original_image,
                ];
            }
        }

        if (! $main_image) {
            throw new BusinessException('请上传商品主图');
        }
        $store_data = array_merge($store_data, ['goods_original' => $main_image, 'goods_thumb' => $main_image]);

        if ($goods_sn) {
            // 修改
            $goods = Goods::query()->whereGoodsSn($goods_sn)->first();

            if (! $goods instanceof Goods) {
                throw new BusinessException('商品不存在');
            }
            $spec_value_service = new GoodsSpecValueService($params, $goods);

            DB::beginTransaction();

            try {
                // 商品信息更新
                if (! $goods->update($store_data)) {
                    throw new BusinessException('商品更新失败');
                }

                // 调整商品详情内容
                if (! $goods->detail()->update(['content' => $params['content']])) {
                    throw new BusinessException('商品详情更新失败');
                }
                // 商品图片信息
                $goods->images()->delete();

                if (! empty($detail_images) && ! $goods->images()->createMany($detail_images)) {
                    throw new BusinessException('商品细节图更新失败');
                }
                // 商品自定义参数信息
                // 商品 spec 处理
                $spec_value_service->exec($goods);
                // 商品 sku 处理

                admin_operation_log($admin_user, "修改了商品信息:{$goods->goods_sn}[{$goods->id}]", AdminOperationLog::TYPE_UPDATE);
                DB::commit();
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
    }
}
