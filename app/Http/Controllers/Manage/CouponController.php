<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends BaseController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        $data = Coupon::query()
            ->when($name, fn ($query) => $query->where('name', 'like', "%{$name}%"))
            ->paginate(10);

        $data->getCollection()->transform(function (Coupon $item) {

            return [
                'id' => $item->id,
                'name' => $item->name,
                'money' => $item->money,
                'number' => $item->number,
                'limit' => $item->limit,
                'style' => $item->style,
                'is_add' => $item->is_add,
                'start_time' => $item->start_time,
                'end_time' => $item->end_time,
                'send_start_time' => date('Y-m-d H:i:s', $item->send_start_time),
                'send_end_time' => date('Y-m-d H:i:s', $item->send_end_time),
                'min_amount' => $item->min_amount,
                'type' => $item->type,
                'created_at' => date('Y-m-d H:i:s', strtotime($item->created_at)),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }
}
