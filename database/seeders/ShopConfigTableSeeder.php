<?php

namespace Database\Seeders;

use App\Models\ShopConfig;
use Illuminate\Database\Seeder;

class ShopConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->addSiteInfo();       // 站点信息
        $this->addSiteLogo();       // 站点Logo
        $this->addSmtpService();    // 邮件服务
        $this->addManageSettings(); // 后台设置
        $this->addIntegralSettings(); // 积分设置
        $this->addSearchSettings(); // 搜索设置
    }

    private function addItem(string $group_name, string $code, mixed $value = null): void
    {
        $shop_config = ShopConfig::query()->firstOrNew(['code' => $code]);

        if (! $shop_config->exists) {
            $shop_config->group_name = $group_name;
            $shop_config->value = $value;
            $shop_config->save();
        }
    }

    private function addSiteInfo(): void
    {
        $this->addItem(ShopConfig::GROUP_SITE_INFO, ShopConfig::SHOP_NAME, '');
        $this->addItem(ShopConfig::GROUP_SITE_INFO, ShopConfig::SHOP_KEYWORDS, '');
        $this->addItem(ShopConfig::GROUP_SITE_INFO, ShopConfig::SHOP_DESCRIPTION, '');
        $this->addItem(ShopConfig::GROUP_SITE_INFO, ShopConfig::BANK_ACCOUNT, '');
        $this->addItem(ShopConfig::GROUP_SITE_INFO, ShopConfig::SHOP_ADDRESS, '');
        $this->addItem(ShopConfig::GROUP_SITE_INFO, ShopConfig::SERVICE_MOBILE, '');
        $this->addItem(ShopConfig::GROUP_SITE_INFO, ShopConfig::ICP_NUMBER, '');
        $this->addItem(ShopConfig::GROUP_SITE_INFO, ShopConfig::SHOP_COLOR, '#E48F34');
        $this->addItem(ShopConfig::GROUP_SITE_INFO, ShopConfig::IS_GRAY, 0);
    }

    private function addSiteLogo(): void
    {
        $this->addItem(ShopConfig::GROUP_SITE_LOGO, ShopConfig::SHOP_ICON, '');
        $this->addItem(ShopConfig::GROUP_SITE_LOGO, ShopConfig::SHOP_LOGO, '');
        $this->addItem(ShopConfig::GROUP_SITE_LOGO, ShopConfig::SHOP_MANAGE_LOGIN_IMAGE, '');
    }

    private function addSmtpService(): void
    {
        $this->addItem(ShopConfig::GROUP_SMTP_SERVICE, ShopConfig::SMTP_HOST, '');
        $this->addItem(ShopConfig::GROUP_SMTP_SERVICE, ShopConfig::SMTP_PORT, '');
        $this->addItem(ShopConfig::GROUP_SMTP_SERVICE, ShopConfig::SMTP_USER, '');
        $this->addItem(ShopConfig::GROUP_SMTP_SERVICE, ShopConfig::SMTP_PASS, '');
    }

    private function addIntegralSettings(): void
    {
        $this->addItem(ShopConfig::GROUP_INTEGRAL, ShopConfig::IS_OPEN_INTEGRAL, '0');
    }

    private function addSearchSettings(): void
    {
        $this->addItem(ShopConfig::GROUP_SEARCH, ShopConfig::SEARCH_DRIVER, '1');
    }

    private function addManageSettings(): void
    {
        $this->addItem(ShopConfig::GROUP_MANAGE_SETTINGS, ShopConfig::MANAGE_LOGIN_RSA_PUBLIC_KEY, '');
        $this->addItem(ShopConfig::GROUP_MANAGE_SETTINGS, ShopConfig::MANAGE_LOGIN_RSA_PRIVATE_KEY, '');
        $this->addItem(ShopConfig::GROUP_MANAGE_SETTINGS, ShopConfig::CURRENCY_FORMAT, '￥%s');
        $this->addItem(ShopConfig::GROUP_MANAGE_SETTINGS, ShopConfig::PRICE_FORMAT, '2');
    }
}
