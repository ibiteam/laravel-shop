<?php

namespace App\Http\Controllers\Seller;

use App\Exceptions\BusinessException;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * 首页.
     */
    public function index(Request $request)
    {
        $seller_user = $this->seller_user();
        if (!$seller_user) {
            return $this->error('商家用户信息错误');
        }

        try {
            $seller_shop = $seller_user->sellerEnter->sellerShop;

            return $this->success($seller_shop);
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取首页数据异常');
        }
    }

}
