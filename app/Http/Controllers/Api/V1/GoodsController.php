<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ApiGoodsDetailResource;
use App\Services\Goods\GoodsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GoodsController extends BaseController
{
    public function detail(Request $request, GoodsService $goods_service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no' => 'required|string',
            ], [], [
                'no' => '商品编号',
            ]);
            $goods = $goods_service->show($validated['no'], $this->user());

            return $this->success(ApiGoodsDetailResource::make($goods));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败'.$throwable->getMessage());
        }
    }
}
