<?php

namespace App\Http\Controllers\Manage\Marketing;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\UserCoupon;
use Illuminate\Http\Request;

class UserCouponController extends BaseController
{
    public function index(Request $request)
    {
        $coupon_id = $request->get('coupon_id');
        $user_name = $request->get('user_name');
        $data = UserCoupon::query()
            ->when($user_name, fn ($query) => $query->whereHas('user', fn ($query) => $query->where('user_name', 'like', "%{$user_name}%")))
            ->when($coupon_id, fn ($query) => $query->where('coupon_id', $coupon_id))
            ->paginate(10);

        $data->getCollection()->transform(function (UserCoupon $item) {
            return [
                'id' => $item->id,
                'name' => "{$item->coupon?->name}",
                'user_name' => "【{$item->user_id}】{$item->user?->user_name}",
                'order_sn' => $item->order?->order_sn,
                'used_time' => $item->used_time ? date('Y-m-d H:i:s', strtotime($item->used_time)) : '',
                'created_at' => date('Y-m-d H:i:s', strtotime($item->created_at)),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }
}
