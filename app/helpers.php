<?php

use App\Enums\CommonEnum;
use App\Http\Dao\AdminOperationLogDao;
use App\Http\Dao\ShopConfigDao;
use App\Models\AdminUser;
use Illuminate\Support\Facades\App;

if (! function_exists('is_local_env')) {
    /**
     * 是否为本地环境.
     */
    function is_local_env(): bool
    {
        return App::isLocal();
    }
}

if (! function_exists('is_test_env')) {
    /**
     * 是否为测试环境.
     */
    function is_test_env(): bool
    {
        return App::environment('test');
    }
}

if (! function_exists('is_pro_env')) {
    /**
     * 是否为生产环境.
     */
    function is_pro_env(): bool
    {
        return App::isProduction();
    }
}

if (! function_exists('shop_config')) {
    /**
     * 获取配置信息.
     */
    function shop_config(string $code, mixed $default = null): mixed
    {
        return app(ShopConfigDao::class)->config($code, $default);
    }
}

if (! function_exists('get_request_ip')) {
    /**
     * 获取请求ip.
     */
    function get_request_ip(): string
    {
        $ip_sources = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR',
        ];
        $ip = '';

        foreach ($ip_sources as $key) {
            if (isset($_SERVER[$key]) && ! empty($_SERVER[$key]) && strcasecmp($_SERVER[$key], 'unknown') !== 0) {
                $ip = $_SERVER[$key];

                break;
            }
        }

        if (! isset($ip) || ! $ip) {
            $ip = request()->getClientIp();
        }

        return strpos($ip, ',') ? substr($ip, 0, strpos($ip, ',')) : $ip;
    }
}

if (! function_exists('is_phone')) {
    /**
     * 判断是否是合法的手机号.
     */
    function is_phone(int|string $phone): bool
    {
        if (! is_numeric($phone)) {
            return false;
        }

        return (bool) preg_match('/^1[3456789]\d{9}$/', $phone);
    }
}

if (! function_exists('get_source')) {
    /**
     * 获取访问来源.
     */
    function get_source(): CommonEnum
    {
        return match (request()->header('source')) {
            'h5' => CommonEnum::H5,
            'pc' => CommonEnum::PC,
            'app' => CommonEnum::APP,
            'wechat_mini' => CommonEnum::WECHAT_MINI,
            default => CommonEnum::H5,
        };
    };
}

if (! function_exists('admin_operation_log')) {
    /**
     * 记录后台管理员操作日志.
     */
    function admin_operation_log(AdminUser $admin_user, string $description, int $type = 0, ?string $table_name = null, int $table_id = 0): void
    {
        app(AdminOperationLogDao::class)->addOperationLogByAdminUser($admin_user, $description, $type, $table_name, $table_id);
    }
}
