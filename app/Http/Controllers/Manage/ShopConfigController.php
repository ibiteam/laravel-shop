<?php

namespace App\Http\Controllers\Manage;

use App\Http\Dao\ShopConfigDao;
use App\Models\AdminOperationLog;
use App\Models\ShopConfig;
use Illuminate\Http\Request;

class ShopConfigController extends BaseController
{
    /**
     * 获取配置信息.
     */
    public function index(Request $request, ShopConfigDao $shop_config_dao)
    {
        $active_name = $request->get('active_name', 'site_info');

        $configCodes = match ($active_name) {
            'site_info' => [// 站点信息
                ShopConfig::SHOP_NAME,
                ShopConfig::BANK_ACCOUNT,
                ShopConfig::SHOP_ADDRESS,
                ShopConfig::SERVICE_MOBILE,
                ShopConfig::SHOP_COLOR,
                ShopConfig::IS_GRAY,
                ShopConfig::ICP_NUMBER,
            ],
            'site_logo' => [// 站点Logo
                ShopConfig::SHOP_LOGO,
                ShopConfig::SHOP_ICON,
                ShopConfig::SHOP_MANAGE_LOGIN_IMAGE,
            ],
            'smtp_service' => [// 邮件服务
                ShopConfig::SMTP_HOST,
                ShopConfig::SMTP_PORT,
                ShopConfig::SMTP_USER,
                ShopConfig::SMTP_PASS,
            ],
        };

        return $this->success($shop_config_dao->getConfigByCodes($configCodes));
    }

    /**
     * 更新配置.
     */
    public function update(Request $request, ShopConfigDao $shop_config_dao)
    {
        $tab_label = $request->get('tab_label');
        $data = $request->all();

        $all_codes = ShopConfig::query()->pluck('code')->toArray();

        foreach (array_keys($data) as $code) {
            if (! in_array($code, $all_codes)) {
                continue;
            }

            ShopConfig::whereCode($code)->update(['value' => json_encode($data[$code] ?? '')]);
        }

        // 删除缓存
        cache_remove('shop_config_all_code');

        // 重新更新缓存
        $shop_config_dao->getAll();

        admin_operation_log($this->adminUser(), "更新了商店设置的【'.$tab_label.'】", AdminOperationLog::TYPE_UPDATE);

        return $this->success('更新成功');
    }
}
