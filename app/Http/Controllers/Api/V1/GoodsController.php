<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ApiGoodsDetailResource;
use App\Http\Resources\CommonResource;
use App\Services\Goods\GoodsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GoodsController extends BaseController
{
    /**
     * 商品详情.
     */
    public function detail(string $no, GoodsService $goods_service): JsonResponse
    {
        try {
            $goods = $goods_service->show($no, $this->user());

            return $this->success(ApiGoodsDetailResource::make($goods));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 根据商品编号和sku唯一值获取sku信息.
     */
    public function skuItem(string $no, string $unique, GoodsService $goods_service): JsonResponse
    {
        try {
            $data = $goods_service->getSkuInfoByNo($no, $unique);

            return $this->success(CommonResource::make($data));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 关注商品
     */
    public function follow(Request $request, GoodsService $goods_service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no' => 'required|string',
            ], [], [
                'no' => '商品编号',
            ]);

            $goods_service->followGoods($validated['no'], $this->user());

            return $this->success('关注成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败'.$throwable->getMessage());
        }
    }

    /**
     * 取消关注商品
     */
    public function unfollow(Request $request, GoodsService $goods_service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no' => 'required|string',
            ], [], [
                'no' => '商品编号',
            ]);
            $goods_service->unfollowGoods($validated['no'], $this->user());

            return $this->success('取消关注成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }
}
