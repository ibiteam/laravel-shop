<?php

namespace App\Http\Controllers\Manage;

use App\Models\SellerEnter;
use Illuminate\Http\Request;

/**
 * 商家入驻列表
 */
class SellerEnterController extends BaseController
{
    public function index(Request $request)
    {
        // if ($request->expectsJson()) {
            $name = $request->get('name');
            $admin_user_name = $request->get('admin_user_name');
            $created_start_time = $request->get('created_start_time');
            $created_end_time = $request->get('created_end_time');
            $number = (int) $request->get('number', '10');

            $base_query = SellerEnter::query()->orderByDesc('id')->with(['adminUser:id,user_name']);

            if ($name) {
                $base_query->where(function ($child_query) use ($name) {
                    return $child_query->where('name', 'LIKE', "%{$name}%")->orWhere('title', 'LIKE', "%{$name}%");
                });
            }

            if ($admin_user_name) {
                $base_query->whereHas('adminUser', function ($created_query) use ($admin_user_name) {
                    return $created_query->where('user_name', 'LIKE', "%{$admin_user_name}%");
                });
            }

            if ($created_start_time) {
                $base_query->where('created_at', '>=', $created_start_time.' 00:00:00');
            }

            if ($created_end_time) {
                $base_query->where('created_at', '<=', $created_end_time.' 23:59:59');
            }

            $data = $base_query->paginate($number);

            // $data->getCollection()->transform(function (SellerEnter $sellerEnter) {
            //     $sellerEnter->admin_user_name = $sellerEnter->adminUser->user_name ?? '';
            //     return $sellerEnter;
            // });

            return $this->success($data->toArray());
        // }

        return view('manage.seller_enter.index');
    }
}
