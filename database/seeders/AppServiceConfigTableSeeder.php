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
        $this->ibi_chat();       // 国联云客服
        $this->geo_amap();       // 地理位置
    }

    /**
     * 新增记录.
     */
    private function addConfig(string $alias, string $name, string $config, int $is_enable = AppServiceConfig::IS_ENABLE, int $is_record = AppServiceConfig::IS_RECORD, int $error_number = 0, int $stop_number = 0)
    {
        $appServiceConfig = AppServiceConfig::firstOrNew([
            'alias' => $alias,
        ]);

        if (! $appServiceConfig->exists) {
            $appServiceConfig->name = $name;
            $appServiceConfig->config = $config;
            $appServiceConfig->is_enable = $is_enable;
            $appServiceConfig->is_record = $is_record;
            $appServiceConfig->error_number = $error_number;
            $appServiceConfig->stop_number = $stop_number;
            $appServiceConfig->desc = '';
            $appServiceConfig->save();
        }
    }

    private function ibi_chat()
    {
        $alias = AppServiceConfig::IBI_CHAT;
        $name = '国联云客服';

        if (is_pro_env()) {
            $config = json_encode([
                'host' => '',
                'platform_id' => '',
                'platform_secret' => '',
            ], JSON_UNESCAPED_UNICODE);
        } else {
            $config = json_encode([
                'host' => 'https://testchat.ptdplat.com/#/client',
                'platform_id' => '174417042419',
                'platform_secret' => '76bad80526f1e149f78051db34f7c9eb',
            ], JSON_UNESCAPED_UNICODE);
        }

        $this->addConfig($alias, $name, $config);
    }

    private function geo_amap()
    {
        $alias = AppServiceConfig::GEO_AMAP;
        $name = '地理位置';

        if (is_pro_env()) {
            $config = json_encode([
                'host' => '',
                'key' => '',
            ], JSON_UNESCAPED_UNICODE);
        } else {
            $config = json_encode([
                'host' => 'https://restapi.amap.com',
                'key' => '46060c0ad9f9298490e225bcb4f7549b',
            ], JSON_UNESCAPED_UNICODE);
        }

        $this->addConfig($alias, $name, $config);
    }
}
