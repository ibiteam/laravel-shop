<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Exceptions\ProcessDataException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\GoodsDao;
use App\Http\Resources\Api\GoodsDetailResource;
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
    public function detail(Request $request, string $no, GoodsService $goods_service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'sku_id' => 'nullable|integer',
            ], [], [
                'sku_id' => 'sku ID',
            ]);
            $goods = $goods_service->show($no, $this->user(), $validated['sku_id'] ?? 0);

            return $this->success(GoodsDetailResource::make($goods));
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
    public function skuItem(string $no, string $unique, GoodsService $goods_service, GoodsDao $goods_dao): JsonResponse
    {
        try {
            $goods = $goods_dao->getInfoByNo($no);

            $data = $goods_service->getSkuInfoByNo($goods, $unique);

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
     * 检查商品库存.
     */
    public function checkNumber(Request $request, string $no, GoodsService $goods_service, GoodsDao $goods_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'sku_id' => 'nullable|integer',
                'number' => 'required|integer',
            ], [], [
                'sku_id' => 'sku ID',
                'number' => '数量',
            ]);
            $goods = $goods_dao->getInfoByNo($no);

            $data = $goods_service->checkGoodsNumber($goods, $validated['sku_id'] ?? 0, $validated['number']);

            return $this->success(CommonResource::make($data));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (ProcessDataException $custom_exception) {
            return $this->failed($custom_exception->getData(), $custom_exception->getMessage(), $custom_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }
}
