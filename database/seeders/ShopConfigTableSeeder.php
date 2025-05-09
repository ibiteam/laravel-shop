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
        $this->addGoodsSettings(); // 商品设置
        $this->addRefundAfterSales(); // 退款售后
        $this->addArticleSettings(); // 文章设置
        $this->addSmsSettings(); // 短信设置
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
        $this->addItem(ShopConfig::GROUP_SITE_INFO, ShopConfig::MANAGE_COLOR, '#1050A9');
        $this->addItem(ShopConfig::GROUP_SITE_INFO, ShopConfig::MOUSE_MOVE_COLOR, '#FFFFFF');
        $this->addItem(ShopConfig::GROUP_SITE_INFO, ShopConfig::IS_GRAY, '0');
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
        $this->addItem(ShopConfig::GROUP_INTEGRAL, ShopConfig::INTEGRAL_NAME, '积分');
    }

    private function addSearchSettings(): void
    {
        $this->addItem(ShopConfig::GROUP_SEARCH, ShopConfig::SEARCH_DRIVER, '1');
    }

    private function addManageSettings(): void
    {
        $this->addItem(ShopConfig::GROUP_MANAGE_SETTINGS, ShopConfig::MANAGE_LOGIN_RSA_PUBLIC_KEY, '');
        $this->addItem(ShopConfig::GROUP_MANAGE_SETTINGS, ShopConfig::MANAGE_LOGIN_RSA_PRIVATE_KEY, '');
    }

    private function addGoodsSettings(): void
    {
        $this->addItem(ShopConfig::GROUP_GOODS, ShopConfig::IS_SHOW_SALES_VOLUME, '0');
        $this->addItem(ShopConfig::GROUP_GOODS, ShopConfig::IS_SHOW_AFTER_SALES, '1');
        $this->addItem(ShopConfig::GROUP_GOODS, ShopConfig::IS_SHOW_EVALUATE, '0');
        $this->addItem(ShopConfig::GROUP_GOODS, ShopConfig::CURRENCY_FORMAT, '￥%s');
        $this->addItem(ShopConfig::GROUP_GOODS, ShopConfig::PRICE_FORMAT, '2');
    }

    private function addRefundAfterSales(): void
    {
        $this->addItem(ShopConfig::GROUP_REFUND_AFTER_SALES, ShopConfig::SELLER_SHIPPED_TIME, '24');
        $this->addItem(ShopConfig::GROUP_REFUND_AFTER_SALES, ShopConfig::SELLER_CONFIRM_TIME, '72');
        $this->addItem(ShopConfig::GROUP_REFUND_AFTER_SALES, ShopConfig::BUYER_CHANGE_TIME, '72');
        $this->addItem(ShopConfig::GROUP_REFUND_AFTER_SALES, ShopConfig::BUYER_REFUND_TIME, '5');
        $this->addItem(ShopConfig::GROUP_REFUND_AFTER_SALES, ShopConfig::SELLER_RECEIVE_TIME, '10');
        $this->addItem(ShopConfig::GROUP_REFUND_AFTER_SALES, ShopConfig::AFTER_SALES_TIMELINESS, '15');
        $this->addItem(ShopConfig::GROUP_REFUND_AFTER_SALES, ShopConfig::AFTER_SALES_MAX_MONEY, '1000');
    }

    private function addArticleSettings(): void
    {
        $this->addItem(ShopConfig::GROUP_ARTICLES, ShopConfig::USER_AGREEMENT, '');
        $this->addItem(ShopConfig::GROUP_ARTICLES, ShopConfig::USER_CANCEL_AGREEMENT, '');
        $this->addItem(ShopConfig::GROUP_ARTICLES, ShopConfig::PRIVACY_POLICY, '');
        $this->addItem(ShopConfig::GROUP_ARTICLES, ShopConfig::ABOUT_US, '');
    }

    private function addSmsSettings(): void
    {
        $this->addItem(ShopConfig::GROUP_SMS, ShopConfig::SMS_DRIVER, 'aliyun');
        $this->addItem(ShopConfig::GROUP_SMS, ShopConfig::SMS_ACCESS_KEY, '');
        $this->addItem(ShopConfig::GROUP_SMS, ShopConfig::SMS_ACCESS_SECRET, '');
        $this->addItem(ShopConfig::GROUP_SMS, ShopConfig::SMS_SIGN_NAME, '');
        $this->addItem(ShopConfig::GROUP_SMS, ShopConfig::SMS_TEMPLATE_PHONE_CODE, '');
    }
}
