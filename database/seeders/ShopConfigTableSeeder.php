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
        $this->addBaseSettings();
        $this->addManageSettings();
    }

    private function addManageSettings(): void
    {
        $this->addItem(ShopConfig::GROUP_MANAGE_SETTINGS, ShopConfig::MANAGE_LOGIN_RSA_PUBLIC_KEY, '');
        $this->addItem(ShopConfig::GROUP_MANAGE_SETTINGS, ShopConfig::MANAGE_LOGIN_RSA_PRIVATE_KEY, '');
    }

    private function addBaseSettings(): void
    {
        $this->addItem(ShopConfig::GROUP_BASE_SETTINGS, ShopConfig::SHOP_NAME, '');
        $this->addItem(ShopConfig::GROUP_BASE_SETTINGS, ShopConfig::SHOP_KEYWORDS, '');
        $this->addItem(ShopConfig::GROUP_BASE_SETTINGS, ShopConfig::SHOP_DESCRIPTION, '');
        $this->addItem(ShopConfig::GROUP_BASE_SETTINGS, ShopConfig::SHOP_ICON, '');
        $this->addItem(ShopConfig::GROUP_BASE_SETTINGS, ShopConfig::SHOP_LOGO, '');
        $this->addItem(ShopConfig::GROUP_BASE_SETTINGS, ShopConfig::SHOP_MANAGE_LOGIN_IMAGE, '');
        $this->addItem(ShopConfig::GROUP_BASE_SETTINGS, ShopConfig::BANK_ACCOUNT, '');
        $this->addItem(ShopConfig::GROUP_BASE_SETTINGS, ShopConfig::SHOP_ADDRESS, '');
        $this->addItem(ShopConfig::GROUP_BASE_SETTINGS, ShopConfig::SERVICE_MOBILE, '');
        $this->addItem(ShopConfig::GROUP_BASE_SETTINGS, ShopConfig::ICP_NUMBER, '');
        $this->addItem(ShopConfig::GROUP_BASE_SETTINGS, ShopConfig::MANAGE_SHOP_COLOR, '#E48F34');
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
}
