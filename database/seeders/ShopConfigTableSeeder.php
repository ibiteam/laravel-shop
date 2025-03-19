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
        $this->addItem(ShopConfig::MANAGE_SETTINGS);
        $this->addItem(ShopConfig::MANAGE_LOGIN_RSA_PUBLIC_KEY, '', ShopConfig::MANAGE_SETTINGS);
        $this->addItem(ShopConfig::MANAGE_LOGIN_RSA_PRIVATE_KEY, '', ShopConfig::MANAGE_SETTINGS);
    }

    private function addBaseSettings(): void
    {
        $this->addItem(ShopConfig::BASE_SETTINGS);
        $this->addItem(ShopConfig::SHOP_NAME, '', ShopConfig::BASE_SETTINGS);
        $this->addItem(ShopConfig::SHOP_KEYWORDS, '', ShopConfig::BASE_SETTINGS);
        $this->addItem(ShopConfig::SHOP_DESCRIPTION, '', ShopConfig::BASE_SETTINGS);
        $this->addItem(ShopConfig::SHOP_ICON, '', ShopConfig::BASE_SETTINGS);
        $this->addItem(ShopConfig::SHOP_LOGO, '', ShopConfig::BASE_SETTINGS);
        $this->addItem(ShopConfig::SHOP_MANAGE_LOGIN_IMAGE, '', ShopConfig::BASE_SETTINGS);
        $this->addItem(ShopConfig::BANK_ACCOUNT, '', ShopConfig::BASE_SETTINGS);
        $this->addItem(ShopConfig::SHOP_ADDRESS, '', ShopConfig::BASE_SETTINGS);
        $this->addItem(ShopConfig::SERVICE_MOBILE, '', ShopConfig::BASE_SETTINGS);
        $this->addItem(ShopConfig::ICP_NUMBER, '', ShopConfig::BASE_SETTINGS);
    }

    private function addItem(string $code, mixed $value = null, string $parent_code = ''): void
    {
        $shop_config = ShopConfig::query()->firstOrNew(['code' => $code]);

        if (! $shop_config->exists) {
            $shop_config->parent_id = $parent_code ? ShopConfig::query()->where('code', $parent_code)->value('id') : 0;
            $shop_config->value = $value;
            $shop_config->save();
        }
    }
}
