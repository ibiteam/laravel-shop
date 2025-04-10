<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Dao\ShopConfigDao;
use App\Models\ShopConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommonController extends BaseController
{
    /**
     * 获取网站配置.
     */
    public function shopConfig(Request $request, ShopConfigDao $shop_config_dao): JsonResponse
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

        return $this->success($data);
    }
}
