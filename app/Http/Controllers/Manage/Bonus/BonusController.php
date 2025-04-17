<?php

namespace App\Http\Controllers\Manage\Bonus;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\Bonus;
use Illuminate\Http\Request;

class BonusController extends BaseController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        $type = $request->get('type');
        $data = Bonus::query()
            ->when($type > -1, fn ($query) => $query->where('type', $type))
            ->when($name, fn ($query) => $query->where('name', 'like', "%{$name}%"))
            ->paginate(10);

        $data->getCollection()->transform(function (Bonus $item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'money' => $item->money,
                'min_amount' => $item->min_amount,
                'type' => $item->type,
                'is_add' => $item->is_add,
                'can_use_coupon' => $item->can_use_coupon,
                'limit' => $item->limit,
                'send_start_time' => date('Y-m-d H:i:s', strtotime($item->send_start_time)),
                'use_start_time' => date('Y-m-d H:i:s', strtotime($item->use_start_time)),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }
}
