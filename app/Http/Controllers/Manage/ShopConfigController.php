<?php

namespace App\Http\Controllers\Manage;

use App\Http\Dao\ShopConfigDao;
use App\Models\AdminOperationLog;
use App\Models\ShopConfig;
use Illuminate\Http\Request;

class ShopConfigController extends BaseController
{
    /**
     * 站点信息.
     */
    public function siteInfo(ShopConfigDao $shop_config_dao)
    {
        $configCodes = [
            ShopConfig::SHOP_NAME,
            ShopConfig::BANK_ACCOUNT,
            ShopConfig::SHOP_ADDRESS,
            ShopConfig::SERVICE_MOBILE,
            ShopConfig::SHOP_COLOR,
            ShopConfig::IS_GRAY,
            ShopConfig::ICP_NUMBER,
        ];

        return $this->success($shop_config_dao->getConfigByCodes($configCodes));
    }

    /**
     * 站点Logo.
     */
    public function siteLogo(ShopConfigDao $shop_config_dao)
    {
        $configCodes = [
            ShopConfig::SHOP_LOGO,
            ShopConfig::SHOP_ICON,
            ShopConfig::SHOP_MANAGE_LOGIN_IMAGE,
        ];

        return $this->success($shop_config_dao->getConfigByCodes($configCodes));
    }


    /**
     * 更新配置.
     */
    public function update(Request $request, ShopConfigDao $shop_config_dao)
    {
        $title = $request->get('title');
        $tab_label = $request->get('tab_label');
        $data = $request->all();

        $all_codes = ShopConfig::query()->pluck('code')->toArray();
        foreach (array_keys($data) as $v) {
            if (!in_array($v, $all_codes)) {
                continue;
            }

            ShopConfig::whereCode($v)->update(['value' => json_encode($data[$v] ?? '')]);
        }

        // 删除缓存
        cache_remove('shop_config_all_code');

        // 重新更新缓存
        $shop_config_dao->getAll();

        admin_operation_log($this->adminUser(), "更新了商店设置的【'.$tab_label.'】", AdminOperationLog::TYPE_UPDATE);

        return $this->success('更新成功');
    }
}
