<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\CommonResourceCollection;
use App\Models\UserIntegral;
use Illuminate\Http\Request;

class UserIntegralController extends BaseController
{
    public function index(Request $request)
    {
        $user = get_user();
        $type = $request->get('type') ?: 0;
        $data = UserIntegral::query()->whereUserId($user?->id)
            ->when($type > 0, fn ($query) => $query->whereType($type))
            ->select(['number', 'type', 'desc', 'created_at'])
            ->latest()->paginate(10);
        $data = (new CommonResourceCollection($data))->toArray($request);

        $data['all_integral'] = $user?->integral;

        return $this->success($data);
    }
}
