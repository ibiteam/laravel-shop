<?php

namespace App\Http\Controllers\Manage;

use App\Http\Resources\CommonResourceCollection;
use App\Models\WechatUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WechatUserController extends BaseController
{
    /**
     * 获取微信用户列表.
     */
    public function index(Request $request): JsonResponse
    {
        $nickname = $request->get('nickname');
        $user_name = $request->get('user_name');
        $is_subscribe = $request->get('is_subscribe');
        $subscribe_start_time = $request->get('subscribe_start_time');
        $subscribe_end_time = $request->get('subscribe_end_time');
        $number = (int) $request->get('limit', 10);

        $list = WechatUser::query()
            ->latest()
            ->latest('id')
            ->with('user:id,user_name')
            ->when($nickname, fn (Builder $query) => $query->where('nickname', 'like', "%{$nickname}%"))
            ->when($user_name, fn (Builder $query) => $query->whereHas('user', fn (Builder $query) => $query->where('user_name', 'like', "%{$user_name}%")))
            ->when(! is_null($is_subscribe), fn (Builder $query) => $query->where('is_subscribe', $is_subscribe))
            ->when($subscribe_start_time, fn (Builder $query) => $query->where('subscribe_time', '>=', $subscribe_start_time))
            ->when($subscribe_end_time, fn (Builder $query) => $query->where('subscribe_time', '<=', $subscribe_end_time))
            ->paginate($number);

        return $this->success(new CommonResourceCollection($list));
    }
}
