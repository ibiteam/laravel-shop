<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\GoodsDao;
use App\Http\Dao\OrderDao;
use App\Http\Dao\OrderEvaluateDao;
use App\Http\Dao\OrderLogDao;
use App\Http\Resources\Api\OrderEvaluateResourceCollection;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderEvaluate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            return $this->success(new OrderEvaluateResourceCollection($list));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 订单评价初始化.
     */
    public function init(Request $request, OrderDao $order_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'order_sn' => 'required|string',
            ], [], [
                'order_sn' => '订单编号',
            ]);
            $current_user = $this->user();

            $order = Order::query()->with('evaluate')->whereUserId($current_user->id)->whereOrderSn($validated['order_sn'])->first();

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            if (! $order_dao->canEvaluate($order)) {
                throw new BusinessException('当前订单已评价或无法评价');
            }

            return $this->success([
                'order_sn' => $order->order_sn,
                'items' => $order
                    ->detail()
                    ->with('goods:id,name,image')
                    ->get()->map(function (OrderDetail $order_detail) {
                        return [
                            'id' => $order_detail->id,
                            'goods_no' => $order_detail->goods_no,
                            'goods_name' => $order_detail->goods_name,
                            'sku_value' => $order_detail->skuValue(),
                            'goods_image' => $order_detail->goods?->image,
                        ];
                    }),
            ]);
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 保存订单评价.
     *
     * @throws \Throwable
     */
    public function store(Request $request, OrderDao $order_dao, OrderLogDao $order_log_dao): JsonResponse
    {
        try {
            $validated = $request->validate([
                'order_sn' => 'required|string',
                'items' => 'required|array',
                'items.*.id' => 'required|integer',
                'items.*.comment' => 'required|string|max:200',
                'items.*.images' => 'nullable|array|max:5',
                'items.*.images.*' => 'required|url',
                'rank' => 'required|integer|between:1,5',
                'goods_rank' => 'required|integer|between:1,5',
                'price_rank' => 'required|integer|between:1,5',
                'bus_rank' => 'required|integer|between:1,5',
                'delivery_rank' => 'required|integer|between:1,5',
                'service_rank' => 'required|integer|between:1,5',
                'is_anonymous' => 'required|boolean',
            ], [
                'items.*.images.max' => '评价图片最多 :max 张',
            ], [
                'order_sn' => '订单编号',
                'items' => '评价明细',
                'items.*.id' => '评价明细ID',
                'items.*.comment' => '评价内容',
                'items.*.images' => '评价图片',
                'items.*.images.*' => '评价图片地址',
                'rank' => '综合评分',
                'goods_rank' => '商品评分',
                'price_rank' => '价格评分',
                'bus_rank' => '商家服务评分',
                'delivery_rank' => '交货速度评分',
                'service_rank' => '服务评分',
                'is_anonymous' => '是否匿名',
            ]);
            $current_user = $this->user();
            $order = Order::query()->with(['evaluate', 'detail'])->whereUserId($current_user->id)->whereOrderSn($validated['order_sn'])->first();

            if (! $order instanceof Order) {
                throw new BusinessException('订单不存在');
            }

            if (! $order_dao->canEvaluate($order)) {
                throw new BusinessException('当前订单已评价或无法评价');
            }
            $store_data = [];

            foreach ($validated['items'] as $item) {
                $order_detail = $order->detail->where('id', $item['id'])->first();

                if (! $order_detail instanceof OrderDetail) {
                    throw new BusinessException('评价明细不存在');
                }
                $store_data[] = [
                    'order_id' => $order->id,
                    'order_detail_id' => $order_detail->id,
                    'goods_id' => $order_detail->goods_id,
                    'is_anonymous' => $validated['is_anonymous'],
                    'status' => OrderEvaluate::STATUS_WAIT,
                    'comment' => $item['comment'],
                    'images' => $item['images'] ?? [],
                    'comment_at' => now()->toDateTimeString(),
                    'rank' => $validated['rank'],
                    'goods_rank' => $validated['goods_rank'],
                    'price_rank' => $validated['price_rank'],
                    'bus_rank' => $validated['bus_rank'],
                    'delivery_rank' => $validated['delivery_rank'],
                    'service_rank' => $validated['service_rank'],
                ];
            }

            if (empty($store_data)) {
                throw new BusinessException('评价明细不能为空');
            }
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }

        DB::beginTransaction();

        try {
            $current_user->evaluates()->createMany($store_data);

            $order_log_dao->storeByUser($current_user, $order, '对订单发表评价');

            DB::commit();

            return $this->success('发表评价成功');
        } catch (BusinessException $business_exception) {
            DB::rollBack();

            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            DB::rollBack();

            return $this->error('操作失败');
        }
    }
}
