<?php

namespace App\Http\Controllers\Manage\Order;

use App\Http\Controllers\Manage\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\OrderEvaluate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvaluateController extends BaseController
{
    /**
     * 评价列表.
     */
    public function index(Request $request): JsonResponse
    {
        $order_no = $request->get('order_no');
        $goods_name = $request->get('goods_name');
        $user_name = $request->get('user_name');
        $evaluate_start_time = $request->get('evaluate_start_time');
        $evaluate_end_time = $request->get('evaluate_end_time');
        $status = $request->get('status');
        $number = (int) $request->get('number', 10);

        $list = OrderEvaluate::query()
            ->latest()
            ->latest('id')
            ->with(['order:id,no', 'goods:id,name,image', 'user:id,user_name'])
            ->when($order_no, fn (Builder $query) => $query->whereHas('order', fn (Builder $query) => $query->where('no', $order_no)))
            ->when($goods_name, fn (Builder $query) => $query->whereHas('goods', fn (Builder $query) => $query->where('name', $goods_name)))
            ->when($user_name, fn (Builder $query) => $query->whereHas('user', fn (Builder $query) => $query->where('user_name', 'like', "%{$user_name}%")))
            ->when(! is_null($status), fn (Builder $query) => $query->where('status', $status))
            ->when($evaluate_start_time, fn (Builder $query) => $query->where('comment_at', '>=', $evaluate_start_time))
            ->when($evaluate_end_time, fn (Builder $query) => $query->where('comment_at', '<=', $evaluate_end_time))
            ->paginate($number);

        return $this->success(new CommonResourceCollection($list));
    }
}
