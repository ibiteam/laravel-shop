<?php

namespace App\Http\Controllers\Manage;

use App\Http\Resources\CommonResourceCollection;
use App\Models\AppDecoration as AppDecorationModel;
use App\Utils\Constant;
use Illuminate\Http\Request;

class AppDecorationController extends BaseController
{
    public function index(Request $request)
    {
        $query = AppDecorationModel::query()
            ->with('adminUser')
            ->withCount('children')
            ->orderBy('id')
            ->whereParentId(Constant::ZERO);
        $list = $query->paginate($request->input('number', 10));
        $list->getCollection()->transform(function (AppDecorationModel $app_decoration) {
            $app_decoration->admin_user_name = $app_decoration->adminUser?->user_name ?? '--';

            return $app_decoration;
        });

        return $this->success(new CommonResourceCollection($list));
    }
}
