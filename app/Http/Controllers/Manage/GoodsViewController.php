<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\GoodsView;
use Illuminate\Http\Request;

class GoodsViewController extends BaseController
{
    public function index(Request $request)
    {
        $keywords = $request->get('keywords');
        $user_name = $request->get('user_name');
        $data = GoodsView::query()
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
            ->with('goods', 'user')
            ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate(10);
        $data->getCollection()->transform(function (GoodsView $item) {
            $goods = $item->goods;
            return [
                'id' => $item->id,
                'referer' => $item->referer,
                'user_name' => $item->user?->user_name,
                'goods_id' => $goods->id,
                'goods_name' => $goods->name,
                'created_at' => date('Y-m-d H:i:s', strtotime($goods->created_at)),
                'updated_at' => date('Y-m-d H:i:s', strtotime($goods->updated_at)),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }
}
