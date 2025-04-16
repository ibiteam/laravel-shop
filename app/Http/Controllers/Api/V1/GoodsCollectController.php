<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\GoodsDao;
use App\Services\Goods\GoodsCollectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GoodsCollectController extends BaseController
{
    /**
     * 关注商品
     */
    public function follow(Request $request, GoodsCollectService $goods_collect_service, GoodsDao $goods_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no' => 'required|string',
            ], [], [
                'no' => '商品编号',
            ]);

            $goods = $goods_dao->getInfoByNo($validated['no']);

            $goods_collect_service->follow($goods, get_user());

            return $this->success('关注成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 取消关注商品
     */
    public function unfollow(Request $request, GoodsCollectService $goods_collect_service, GoodsDao $goods_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no' => 'required|string',
            ], [], [
                'no' => '商品编号',
            ]);
            $goods = $goods_dao->getInfoByNo($validated['no']);

            $goods_collect_service->unfollow($goods, get_user());

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
