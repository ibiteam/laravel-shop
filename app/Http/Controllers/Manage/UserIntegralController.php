<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Manage\IndexRequest;
use App\Http\Resources\CommonResourceCollection;
use App\Models\User;
use App\Models\UserIntegral;

class UserIntegralController extends BaseController
{
    public function index(IndexRequest $request)
    {
        $keywords = $request->get('keywords');
        $data = User::query()
            ->when($keywords, fn ($query) => $query->where('name', 'like', "%{$keywords}%")->orWhere('id', 'like', "%{$keywords}%"))
            ->where('integral', '>', 0)
            ->latest()->paginate($request->per_page);
        $data->getCollection()->transform(function (User $item) {
            return [
                'id' => $item->id,
                'user_name' => $item->user_name,
                'integral' => $item->integral,
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }

    public function detail(IndexRequest $request)
    {
        $user_name = $request->get('user_name');
        $type = $request->get('type');
        $data = UserIntegral::query()
            ->when($user_name, fn ($query) => $query
                ->whereHas('user', fn ($query) => $query->where('user_name', 'like', "%{$user_name}%"))
            )
            ->when($type, fn ($query) => $query->where('type', $type))
            ->with('user')
            ->latest()->paginate($request->per_page);
        $data->getCollection()->transform(function (UserIntegral $item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'number' => $item->number,
                'user_name' => $item->user?->user_name,
                'type' => $item->type,
                'desc' => $item->desc,
                'created_at' => date('Y-m-d H:i:s', strtotime($item->created_at)),
                'updated_at' => date('Y-m-d H:i:s', strtotime($item->updated_at)),
            ];
        });

        return $this->success(new CommonResourceCollection($data));
    }
}
