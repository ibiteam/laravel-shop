<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\GoodsDao;
use App\Http\Dao\OrderEvaluateDao;
use App\Http\Resources\ApiOrderEvaluateResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EvaluateController extends BaseController
{
    /**
     * 商品评价列表.
     */
    public function indexByGoods(Request $request, GoodsDao $goods_dao, OrderEvaluateDao $order_evaluate_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'no' => 'required|string',
                'page' => 'required|integer|min:1',
                'number' => 'required|integer|min:1',
            ], [], [
                'no' => '商品编号',
                'page' => '页码',
                'number' => '每页数量',
            ]);
            $goods = $goods_dao->getInfoByNo($validated['no']);

            $list = $order_evaluate_dao->getListByGoodsId($goods->id, $validated['page'], $validated['number']);

            return $this->success(new ApiOrderEvaluateResourceCollection($list));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    // 订单评价初始化
    public function evaluateInit(Request $request)
    {
        //
    }

    // 保存订单评价
    public function store(Request $request)
    {
        //
    }
}
