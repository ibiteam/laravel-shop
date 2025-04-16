<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\GoodsView;
use Illuminate\Http\Request;

class GoodsViewController extends BaseController
{
    public function index()
    {
        $user = $this->user();
        $data = GoodsView::query()
            ->whereUserId($user?->id)
            ->with('goods')
            ->orderByDesc('updated_at')
            ->paginate(10);
        $data->getCollection()->transform(function (GoodsView $item) {
            $goods = $item->goods;
            return [
                'image' => $goods->image,
                'price' => $goods->price,
                'goods_name' => $goods->name,
                'goods_no' => $goods->no,
                'unit' => $goods->unit,
                'updated_at' => date('Y-m-d H:i:s', strtotime($goods->updated_at)),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }
}
