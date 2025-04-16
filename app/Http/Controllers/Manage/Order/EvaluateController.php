<?php

namespace App\Http\Controllers\Manage\Order;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Manage\BaseController;
use App\Http\Resources\CommonResource;
use App\Http\Resources\CommonResourceCollection;
use App\Models\OrderEvaluate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EvaluateController extends BaseController
{
    /**
     * 评价列表.
     */
    public function index(Request $request): JsonResponse
    {
        $order_sn = $request->get('order_sn');
        $goods_name = $request->get('goods_name');
        $user_name = $request->get('user_name');
        $evaluate_start_time = $request->get('evaluate_start_time');
        $evaluate_end_time = $request->get('evaluate_end_time');
        $status = $request->get('status');
        $number = (int) $request->get('number', 10);

        $list = OrderEvaluate::query()
            ->latest()
            ->latest('id')
            ->with(['order:id,order_sn', 'goods:id,name,image', 'user:id,user_name'])
            ->when($order_sn, fn (Builder $query) => $query->whereHas('order', fn (Builder $query) => $query->where('order_sn', $order_sn)))
            ->when($goods_name, fn (Builder $query) => $query->whereHas('goods', fn (Builder $query) => $query->where('name', $goods_name)))
            ->when($user_name, fn (Builder $query) => $query->whereHas('user', fn (Builder $query) => $query->where('user_name', 'like', "%{$user_name}%")))
            ->when(! is_null($status), fn (Builder $query) => $query->where('status', $status))
            ->when($evaluate_start_time, fn (Builder $query) => $query->where('comment_at', '>=', $evaluate_start_time))
            ->when($evaluate_end_time, fn (Builder $query) => $query->where('comment_at', '<=', $evaluate_end_time))
            ->paginate($number);

        return $this->success(new CommonResourceCollection($list));
    }

    /**
     * 审核.
     */
    public function check(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'status' => 'required|integer|in:'.implode(',', [OrderEvaluate::STATUS_SUCCESS, OrderEvaluate::STATUS_REJECT]),
            ], [], [
                'id' => '评价ID',
                'status' => '状态',
            ]);
            $order_evaluate = OrderEvaluate::query()->whereId($validated['id'])->first();

            if (! $order_evaluate instanceof OrderEvaluate) {
                throw new BusinessException('评价不存在');
            }
            $tmp_status = (int) $validated['status'];

            if ($order_evaluate->status === $tmp_status) {
                throw new BusinessException('评价无需重新审核');
            }
            $current_user = $this->adminUser();

            if ($tmp_status === OrderEvaluate::STATUS_SUCCESS) {
                $tmp_message = "将订单评价[{$order_evaluate->id}],审核通过";
            } else {
                $tmp_message = "将订单评价[{$order_evaluate->id}],审核驳回";
            }

            if (! $order_evaluate->update(['status' => $validated['status']])) {
                throw new BusinessException('审核失败');
            }
            admin_operation_log($tmp_message);

            return $this->success('操作审核成功');
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }

    /**
     * 详情.
     */
    public function detail(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
            ], [], [
                'id' => '评价ID',
            ]);
            $order_evaluate = OrderEvaluate::query()->with(['order:id,order_sn', 'goods:id,name,image', 'user:id,user_name'])->whereId($validated['id'])->first();

            if (! $order_evaluate instanceof OrderEvaluate) {
                throw new BusinessException('订单评价不存在');
            }

            return $this->success(CommonResource::make($order_evaluate));
        } catch (ValidationException $validation_exception) {
            return $this->error($validation_exception->validator->errors()->first());
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('操作失败');
        }
    }
}
