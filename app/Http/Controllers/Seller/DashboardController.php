<?php

namespace App\Http\Controllers\Seller;

use App\Exceptions\BusinessException;
use App\Models\SellerShop;
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
            $seller_shop = SellerShop::query()->select(['id', 'seller_id', 'name', 'logo'])
                ->whereSellerId($seller_user->seller_id)->first();
            if (!$seller_shop) {
                throw new BusinessException('商家店铺不存在');
            }

            $data['seller_shop'] = $seller_shop;

            return $this->success($data);
        } catch (BusinessException $business_exception) {
            return $this->error($business_exception->getMessage(), $business_exception->getCodeEnum());
        } catch (\Throwable $throwable) {
            return $this->error('获取首页数据异常');
        }
    }

}
