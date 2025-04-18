<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\UserBonus;
use Illuminate\Http\Request;

class UserBonusController extends BaseController
{
    public function index(Request $request)
    {
        $bonus_id = $request->get('bonus_id');
        $user_name = $request->get('user_name');
        $data = UserBonus::query()
            ->when($user_name, fn ($query) => $query->whereHas('user', fn ($query) => $query->where('user_name', 'like', "%{$user_name}%")))
            ->when($bonus_id, fn ($query) => $query->where('bonus_id', $bonus_id))
            ->paginate(10);

        $data->getCollection()->transform(function (UserBonus $item) {
            return [
                'id' => $item->id,
                'name' => "{$item->bonus?->name}",
                'user_name' => "【{$item->user_id}】{$item->user?->user_name}",
                'order_sn' => $item->order?->order_sn,
                'used_time' => $item->used_time ? date('Y-m-d H:i:s', strtotime($item->used_time)) : '',
                'created_at' => date('Y-m-d H:i:s', strtotime($item->created_at)),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }
}
