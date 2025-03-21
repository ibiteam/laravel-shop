<?php

use App\Enums\CommonEnum;
use App\Http\Dao\AdminOperationLogDao;
use App\Http\Dao\ShopConfigDao;
use App\Services\MobileRouterService;
use App\Utils\Constant;
use App\Models\AdminUser;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

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
    }
}

if (! function_exists('connectStr')) {
    /**
     * 获取路径地址连接符.
     */
    function connectStr($url)
    {
        if (strpos($url, '?')) {
            return '&';
        }

        return '?';
    }
}
if (! function_exists('cache_forever')) {
    /**
     * 永久缓存.
     *
     * @param $code
     */
    function cache_forever($key, $callback)
    {
        //测试环境数据不缓存
        if (config('app.debug')) {
            return $callback();
        }

        return Cache::rememberForever(config('cache.prefix').$key, $callback);
    }
}

if (! function_exists('is_m_request')) {
    /**
     * 判断来源是否为H5.
     */
    function is_m_request(): bool
    {
        if (strtoupper(request()->header('Access-From', '')) == Constant::REFERER_H5) {
            return true;
        }
        $m_host = shop_config(ShopConfig::M_URL);

        if (! $m_host) {
            return false;
        }

        return str_contains(request()->fullUrl(), $m_host);
    }
}

if (! function_exists('is_app_request')) {
    /**
     * 判断来源是否为APP.
     */
    function is_app_request(): bool
    {
        if (strtoupper(request()->header('Access-From', '')) === Constant::REFERER_APP) {
            return true;
        }

        return false;
    }
}

if (! function_exists('is_miniProgram_request')) {
    /**
     * 请求的来源是否是小程序.
     */
    function is_miniProgram_request(): bool
    {
        /* deleted start */
        $needle = 'miniProgram';

        // 原小程序接口
        if (str_contains(request()->fullUrl(), $needle)) {
            return true;
        }

        // 新小程序接口
        if (request()->header('Platform-Type', '') === $needle) {
            return true;
        }

        /* deleted end */
        if (strtoupper(request()->header('Access-From', '')) === Constant::REFERER_MINI) {
            return true;
        }

        return false;
    }
}


if (! function_exists('is_harmony_request')) {
    /**
     * 判断是否为鸿蒙系统请求.
     */
    function is_harmony_request(): bool
    {
        if (strtoupper(request()->header('System-Type', '')) === 'HARMONYOS') {
            return true;
        }

        if (request()->header('System-Source') == 'harmonyOs') {
            return true;
        }
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        return str_contains($agent, 'harmonyOs');
    }
}

if (! function_exists('is_android_request')) {
    /**
     * 判断是否为安卓系统请求
     */
    function is_android_request(): bool
    {
        return strtoupper(request()->header('System-Type', '')) === 'ANDROID';
    }
}

if (! function_exists('is_ios_request')) {
    /**
     * 判断是否为IOS系统请求
     */
    function is_ios_request(): bool
    {
        return strtoupper(request()->header('System-Type', '')) === 'IOS';
    }
}

if (! function_exists('sourcePort')) {
    /**
     * 获取来源.
     */
    function sourcePort()
    {
        if (is_miniProgram_request()) {
            return MobileRouterService::SOURCE_MINI;
        } elseif (is_m_request()) {
            return MobileRouterService::SOURCE_H5;
        } elseif (is_app_request()) {
            return MobileRouterService::SOURCE_APP;
        }

        return '';
    }
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
