<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;

class GoodsController extends BaseController
{
    public function index(Request $request)
    {
        //
    }

    public function create(Request $request)
    {
        return view('manage.goods.goods_from');
    }
}
