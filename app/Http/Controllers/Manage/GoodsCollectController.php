<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\GoodsCollect;
use Illuminate\Http\Request;

class GoodsCollectController extends BaseController
{
    // 收藏列表
    public function index(Request $request)
    {
        $keywords = $request->get('keywords');
        $user_name = $request->get('user_name');
        $data = GoodsCollect::query()
            ->when($keywords, fn ($query) => $query
                ->whereHas('goods', fn ($query) => $query
                    ->where(fn ($query) => $query
                        ->where('name', 'like', "%{$keywords}%")
                        ->orWhere('id', 'like', "%{$keywords}%"))
                )
            )
            ->when($user_name, fn ($query) => $query
                ->whereHas('user', fn ($query) => $query->where('user_name', 'like', "%{$user_name}%"))
            )
            ->whereIsAttention(GoodsCollect::ATTENTION_YES)
            ->with('goods', 'user')
            ->latest()->paginate(10);

        $data->getCollection()->transform(function (GoodsCollect $goods_collect) {
            $goods = $goods_collect->goods;

            return [
                'id' => $goods_collect->id,
                'user_name' => $goods_collect->user?->user_name,
                'goods_id' => $goods->id,
                'goods_name' => $goods->name,
                'created_at' => date('Y-m-d H:i:s', strtotime($goods->created_at)),
                'updated_at' => date('Y-m-d H:i:s', strtotime($goods->updated_at)),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }
}
