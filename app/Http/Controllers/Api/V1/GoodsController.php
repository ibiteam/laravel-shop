<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ApiGoodsDetailResource;
use App\Http\Resources\CommonResource;
use App\Services\Goods\GoodsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class GoodsController extends BaseController
{
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
            return $this->error('操作失败'.$throwable->getMessage());
        }
    }

    public function skuItem(string $no, string $unique, GoodsService $goods_service)
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
}
