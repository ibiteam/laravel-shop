<?php

namespace App\Http\Controllers\Manage;

use App\Http\Resources\ManageOrderResourceCollection;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    public function index(Request $request)
    {
        $no = $request->get('no');
        $user_keywords = $request->get('user_keywords');
        $goods_id = $request->get('goods_id');
        $goods_name = $request->get('goods_name');
        $consignee_keywords = $request->get('consignee_keywords');
        $order_status = $request->get('order_status');
        $pay_status = $request->get('pay_status');
        $shipping_status = $request->get('shipping_status');
        $done_start_time = $request->get('done_start_time');
        $done_end_time = $request->get('done_end_time');
        $source = $request->get('source');
        $number = (int) $request->get('number', 10);

        $list = Order::query()
            ->with('user:id,user_name')
            ->when(! is_null($no), fn (Builder $query) => $query->where('no', $no))
            ->when(! is_null($order_status), fn (Builder $query) => $query->where('order_status', $order_status))
            ->when(! is_null($pay_status), fn (Builder $query) => $query->where('pay_status', $pay_status))
            ->when(! is_null($shipping_status), fn (Builder $query) => $query->where('ship_status', $shipping_status))
            ->when(! is_null($done_start_time), fn (Builder $query) => $query->where('created_at', '>=', $done_start_time))
            ->when(! is_null($done_end_time), fn (Builder $query) => $query->where('created_at', '<=', $done_end_time))
            ->when(! is_null($source), fn (Builder $query) => $query->where('source', $source))
            ->when(! is_null($consignee_keywords), fn (Builder $query) => $query->where(function (Builder $query) use ($consignee_keywords) {
                $query->whereLike('consignee', "%$consignee_keywords%")->orWhereLike('phone', "%$consignee_keywords%");
            }))
            ->when(! is_null($goods_name), fn (Builder $query) => $query->whereHas('detail', function ($query) use ($goods_name) {
                $query->whereLike('goods_name', "%$goods_name%");
            }))
            ->when(! is_null($goods_id), fn (Builder $query) => $query->whereHas('detail', function ($query) use ($goods_id) {
                $query->where('goods_id', $goods_id);
            }))
            ->when(! is_null($user_keywords), function (Builder $query) use ($user_keywords) {
                $query->where('user_id', $user_keywords)->orWhereHas('user', function ($query) use ($user_keywords) {
                    $query->whereLike('nickname', "%$user_keywords%");
                });
            })
            ->latest()
            ->paginate($number);

        return $this->success(new ManageOrderResourceCollection($list));
    }
}
