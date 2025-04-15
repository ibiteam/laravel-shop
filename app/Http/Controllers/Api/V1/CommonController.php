<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\CartDao;
use App\Http\Dao\ShopConfigDao;
use App\Models\ShopConfig;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommonController extends BaseController
{
    /**
     * 获取网站配置.
     */
    public function shopConfig(Request $request, ShopConfigDao $shop_config_dao, CartDao $cart_dao): JsonResponse
    {
        $data = $shop_config_dao->multipleConfig(
            ShopConfig::INTEGRAL_NAME,
            ShopConfig::SHOP_NAME,
            ShopConfig::SHOP_KEYWORDS,
            ShopConfig::SHOP_DESCRIPTION,
            ShopConfig::IS_GRAY,
            ShopConfig::SHOP_COLOR,
            ShopConfig::SHOP_LOGO
        );
        $data['cart_count'] = 0;

        $current_user = $this->user();

        if ($current_user instanceof User) {
            $data['cart_count'] = $cart_dao->getValidCarNumber($current_user->id);
        }

        // 微信公众号 APP id
        $data['wechat_app_id'] = config('easywechat.official_account.default.app_id');

        return $this->success($data);
    }
}
