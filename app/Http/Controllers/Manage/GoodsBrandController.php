<?php

namespace App\Http\Controllers\Manage;

use App\Http\Resources\CommonResourceCollection;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class GoodsBrandController extends BaseController
{
    public function index(Request $request)
    {
        $is_show = $request->get('is_show', -1);
        $number = (int) $request->get('number', 10);
        $list = Brand::query()->latest()
            ->when($name = $request->get('name'), fn (Builder $query) => $query->whereLike('name', "%{$name}%"))
            ->when($is_show >= 0, fn (Builder $query) => $query->where('is_show', $is_show))
            ->paginate($number);

        return $this->success(new CommonResourceCollection($list));
    }
}
