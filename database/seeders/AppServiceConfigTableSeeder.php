<?php

namespace Database\Seeders;

use App\Models\AppServiceConfig;
use Illuminate\Database\Seeder;

class AppServiceConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->addConfig(AppServiceConfig::IBI_CHAT, '国联云客服', ['host' => '', 'platform_id' => '', 'platform_secret' => '']);
        $this->addConfig(AppServiceConfig::GEO_AMAP, '地理位置', ['host' => '', 'key' => '']);
        $this->addConfig(AppServiceConfig::KUAI_DI_100, '快递100', ['host' => '', 'key' => '', 'customer' => '']);
    }

    /**
     * 新增记录.
     */
    private function addConfig(string $alias, string $name, array $config, int $is_enable = AppServiceConfig::IS_ENABLE, int $is_record = AppServiceConfig::IS_RECORD, int $error_number = 0, int $stop_number = 0)
    {
        $appServiceConfig = AppServiceConfig::firstOrNew([
            'alias' => $alias,
        ]);

        if (! $appServiceConfig->exists) {
            $appServiceConfig->name = $name;
            $appServiceConfig->config = json_encode($config, JSON_UNESCAPED_UNICODE);
            $appServiceConfig->is_enable = $is_enable;
            $appServiceConfig->is_record = $is_record;
            $appServiceConfig->error_number = $error_number;
            $appServiceConfig->stop_number = $stop_number;
            $appServiceConfig->desc = '';
            $appServiceConfig->save();
        }
    }
}
