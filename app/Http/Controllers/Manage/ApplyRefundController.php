<?php

namespace App\Http\Controllers\Manage;

use App\Http\Resources\CommonResourceCollection;
use App\Models\ApplyRefund;
use Illuminate\Http\Request;

// 退款原因
class ApplyRefundController extends BaseController
{
    /**
     * 列表.
     */
    public function index(Request $request)
    {
        $user_name = $request->get('user_name', '');
        $goods_name = $request->get('goods_name', '');
        $order_no = $request->get('order_no', '');
        $no = $request->get('no', '');
        $status = $request->get('status', null);
        $type = $request->get('type', null);
        $start_time = $request->get('start_time', '');
        $end_time = $request->get('end_time', '');
        $number = (int) $request->get('number', 10);

        $data = ApplyRefund::query()
            ->with(['user', 'order', 'orderDetail', 'applyRefundReason'])
            ->when($user_name, fn ($query) => $query->whereHas('user', fn ($query) => $query->where('user_name', 'like', '%'.$user_name.'%')))
            ->when($goods_name, fn ($query) => $query->whereHas('orderDetail', fn ($query) => $query->where('goods_name', 'like', '%'.$goods_name.'%')))
            ->when($order_no, fn ($query) => $query->whereHas('order', fn ($query) => $query->where('no', 'like', '%'.$order_no.'%')))
            ->when($no, fn ($query) => $query->where('no', 'like', '%'.$no.'%'))
            ->when(! is_null($status), fn ($query) => $query->where('status', '=', $status))
            ->when(! is_null($type), fn ($query) => $query->where('type', '=', $type))
            ->when($start_time, fn ($query) => $query->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($start_time))))
            ->when($end_time, fn ($query) => $query->where('created_at', '<=', date('Y-m-d H:i:s', strtotime($end_time))))
            ->orderByDesc('created_at')->paginate($number);
        $data->getCollection()->transform(function (ApplyRefund $apply_refund) {
            return [
                'id' => $apply_refund->id,
                'no' => $apply_refund->no,
                'user_name' => $apply_refund->user?->user_name,
                'goods_name' => $apply_refund->orderDetail?->goods_name,
                'order_no' => $apply_refund->order?->no,
                'type' => strval($apply_refund->type),
                'status' => strval($apply_refund->status),
                'money' => $apply_refund->money,
                'number' => $apply_refund->number,
                'reason' => $apply_refund->applyRefundReason?->content,
                'description' => $apply_refund->description,
                'certificate' => $apply_refund->certificate,
                'is_revoke' => strval($apply_refund->is_revoke),
                'count' => $apply_refund->count,
                'result' => $apply_refund->result,
                'created_at' => $apply_refund->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $apply_refund->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }
}
