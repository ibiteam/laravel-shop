<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * 首页.
     */
    public function index(Request $request)
    {
        $user = $this->user();
        $seller_shop = $user->sellerEnter->sellerShop;

        return $this->success([]);
    }

}
