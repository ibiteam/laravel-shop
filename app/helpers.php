<?php

use App\Enums\CommonEnum;
use App\Http\Dao\AdminOperationLogDao;
use App\Http\Dao\ShopConfigDao;
use App\Models\AdminUser;
use App\Models\SensitiveWord;
use App\Models\ShopConfig;
use App\Services\MobileRouterService;
use App\Utils\Constant;
use App\Utils\Sensitive\Helper as SensitiveHelper;
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

if (! function_exists('is_md5')) {
    /**
     * 判断字符串是否是MD5字符串.
     */
    function is_md5(string $str): bool
    {
        return (bool) preg_match('/^[a-z0-9]{32}$/', $str);
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
    function admin_operation_log(AdminUser $admin_user, string $description, int $type = 0): void
    {
        app(AdminOperationLogDao::class)->addOperationLogByAdminUser($admin_user, $description, $type);
    }
}

if (! function_exists('format_number')) {
    /**
     * 格式化数量.
     *
     * @return float|int|mixed|string
     */
    function format_number($numb)
    {
        if (! is_numeric($numb)) {
            return $numb;
        }

        if ($numb > 10000 && $numb < 100000000) {
            $numb = round($numb / 10000, 2).'万';
        } elseif ($numb >= 100000000) {
            $numb = round($numb / 100000000, 2).'亿';
        }

        return $numb;
    }
}

if (! function_exists('is_mobile_request')) {
    /**
     * 判断是否是手机访问.
     */
    function is_mobile_request()
    {
        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
        $mobile_browser = '0';

        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'] ?? ''))) {
            $mobile_browser++;
        }

        if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false)) {
            $mobile_browser++;
        }

        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            $mobile_browser++;
        }

        if (isset($_SERVER['HTTP_PROFILE'])) {
            $mobile_browser++;
        }
        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 4));
        $mobile_agents = [
            'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
            'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
            'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
            'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
            'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
            'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
            'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
            'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
            'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-',
        ];

        if (in_array($mobile_ua, $mobile_agents)) {
            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) {
            $mobile_browser++;
        }

        // Pre-final check to reset everything if the user is on Windows
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT'] ?? ''), 'windows') !== false) {
            $mobile_browser = 0;
        }

        // But WP7 is also Windows, with a slightly different characteristic
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT'] ?? ''), 'windows phone') !== false) {
            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['USER_AGENT'] ?? ''), 'android') !== false) {
            $mobile_browser++;
        }

        if ($mobile_browser > 0) {
            return true;
        }

        return false;
    }
}

if (! function_exists('phone_hidden')) {
    /**
     * 隐藏手机号中间4位.
     */
    function phone_hidden($phone)
    {
        if (! $phone) {
            return '';
        }

        return preg_replace('/(\d{3})\d{4}(\d{4})/', '$1****$2', $phone);
    }
}

if (! function_exists('get_sensitive_words')) {
    /**
     * 检测文字中的敏感词，存在返回数组.
     */
    function get_sensitive_words($content): array
    {
        if (! $content) {
            return [];
        }
        $sensitiveWords = SensitiveWord::query()->pluck('name')->toArray();

        try {
            return SensitiveHelper::getInstance()->setTree($sensitiveWords)->getBadWord($content);
        } catch (\Exception) {
            return [];
        }
    }
}


if (! function_exists('is_spider')) {
    /**
     * 是否是搜索引擎抓取.
     */
    function is_spider(): bool
    {
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        //搜索引擎
        $spiders = [
            '/sogou/i',
            '/bing/i',
            '/baidu/i',
            '/google/i',
            '/360/i',
            '/soso/i',
            '/msn/i',
            '/ask/i',
            '/Bot/i',
            '/yahoo/i',
            '/youdao/i',
            '/AhrefsBot/i',
            '/YisouSpider/i',
            '/SemrushBot/i',
            '/DotBot/i',
            '/Bytespider/i',
            '/YexBot/i',
            '/zoominfobot/i',
            '/Applebot/i',
            '/MJ12bot/i',
            '/YexBot/i',
            '/Daum/i',
            '/Trident\/4.0/i',
            '/Gecko\/20100101/i',
            '/Barkrowler/i',
        ];

        return ! is_null(Arr::first($spiders, function ($spider) use ($agent) {
            return (bool) preg_match($spider, $agent);
        }));
    }
}

if (! function_exists('price_format')) {
    /**
     * 对价格进行格式化.
     */
    function price_format($price, $currency_format = ''): int|string
    {
        if ($price === '') {
            return 0;
        }

        if ($currency_format === '') {
            $currency_format = shop_config(ShopConfig::CURRENCY_FORMAT);
        }

        return sprintf($currency_format, $price);
    }
}

if (! function_exists('price_number_format')) {
    /**
     * 对价格进行格式化并添加货币符号 保留2位或者后台配置的小数.
     */
    function price_number_format($price): int|string
    {
        return price_format(to_number_format($price));
    }
}

if (! function_exists('to_number_format')) {
    /**
     * 对价格进行格式化 保留2位或者后台配置的小数.
     */
    function to_number_format($price, $config_price_format = '', $thousands_separator = ''): string
    {
        if (! $config_price_format) {
            $config_price_format = shop_config(ShopConfig::PRICE_FORMAT);
        }

        return number_format($price, $config_price_format ?: 2, '.', $thousands_separator);
    }
}
