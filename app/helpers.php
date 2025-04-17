<?php

use App\Enums\RefererEnum;
use App\Http\Dao\AdminOperationLogDao;
use App\Http\Dao\ShopConfigDao;
use App\Models\AdminUser;
use App\Models\SensitiveWord;
use App\Models\ShopConfig;
use App\Models\User as UserModel;
use App\Utils\Constant;
use App\Utils\Sensitive\Helper as SensitiveHelper;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

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
            $tmp_ip = $_SERVER[$key] ?? '';

            if (! empty($tmp_ip) && strcasecmp($tmp_ip, 'unknown') !== 0) {
                $ip = $tmp_ip;

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
    function get_source(): RefererEnum
    {
        return RefererEnum::formSource(request()->header('source', ''));
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

if (! function_exists('get_url_joiner')) {
    /**
     * 获取路径地址连接符.
     */
    function get_url_joiner($url): string
    {
        if (strpos($url, '?')) {
            return '&';
        }

        return '?';
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

if (! function_exists('admin_operation_log')) {
    /**
     * 记录后台管理员操作日志.
     */
    function admin_operation_log(string $description, int $type = 0): void
    {
        $admin_user_id = get_admin_user()->id ?? 0;
        app(AdminOperationLogDao::class)->addOperationLogByAdminUser($admin_user_id, $description, $type);
    }
}

if (! function_exists('get_admin_user')) {
    /**
     * 获取管理员信息.
     */
    function get_admin_user(): ?AdminUser
    {
        return Auth::guard(config('auth.manage.guard'))->user();
    }
}

if (! function_exists('get_user')) {
    /**
     * 获取前台用户.
     */
    function get_user(): ?UserModel
    {
        return Auth::guard(config('auth.api.guard'))->user();
    }
}

if (! function_exists('phone_hidden')) {
    /**
     * 隐藏手机号中间4位.
     */
    function phone_hidden($phone): string
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
        // 搜索引擎
        $spiders = [
            '/sogou/i', '/bing/i', '/baidu/i', '/google/i', '/360/i', '/soso/i',
            '/msn/i', '/ask/i', '/Bot/i', '/yahoo/i', '/youdao/i', '/AhrefsBot/i',
            '/YisouSpider/i', '/SemrushBot/i', '/DotBot/i', '/Bytespider/i', '/YexBot/i',
            '/zoominfobot/i', '/Applebot/i', '/MJ12bot/i', '/YexBot/i', '/Daum/i', '/Trident\/4.0/i',
            '/Gecko\/20100101/i', '/Barkrowler/i',
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

if (! function_exists('chinese_number_down_format')) {
    /**
     * 中文数字下标格式化.
     */
    function chinese_number_down_format(int $number): string
    {
        $unit = '';

        if ($number == 0) {
            return (string) $number;
        }

        if ($number >= 100000000) {
            $fixed_number = 100000000;
            $unit = '亿+';
        } elseif ($number >= 10000) {
            $fixed_number = 10000;
            $unit = '万+';
        } elseif ($number >= 1000) {
            $fixed_number = 1;
            $unit = '+';
        } else {
            $fixed_number = 1;
        }

        return (string) (floor($number / $fixed_number).$unit);
    }
}

if (! function_exists('get_flow_sn')) {
    /**
     * 获取流水号.
     */
    function get_flow_sn(): string
    {
        /* 选择一个随机的方案 */
        mt_srand((int) ((float) microtime() * 1000000));
        $del = mt_rand(1000, 9999);
        $mul = mt_rand(10, 99);
        $first = [6, 7, 8, 9];
        $key = array_rand($first, 1);

        return date('Ymd').$key.substr((string) ($del * $mul), 0, 6).$mul;
    }
}

if (! function_exists('get_new_price')) {
    /**
     * 处理浮点数中的0 12.00 转为12  12.40 转为 12.4  12.04 不转  12.04.
     */
    function get_new_price($num): string
    {
        $arrayNum = explode('.', (string) $num);

        if (isset($arrayNum[1]) && preg_match('/[1-9]/', $arrayNum[1])) {
            $arrayNum[1] = rtrim($arrayNum[1], (string) 0);
        } else {
            unset($arrayNum[1]);
        }

        return implode('.', $arrayNum);
    }
}

/**
 * 系统获取:浏览器信息获取|当前访问系统获取|当前访问IP获取.
 */
if (! function_exists('get_custom_browser')) {
    /**
     * 获取客户端浏览器信息.
     */
    function get_custom_browser(): string
    {
        $system = $_SERVER['HTTP_USER_AGENT'] ?? '';  // 获取用户代理字符串

        // 火狐
        if (stripos($system, 'Firefox/') > 0) {
            preg_match("/Firefox\/([^;)]+)+/i", $system, $matches);

            return 'Firefox('.($matches[1] ?? '').')';
        }

        // 傲游
        if (stripos($system, 'Maxthon') > 0) {
            preg_match("/Maxthon\/([\d\.]+)/", $system, $matches);

            return '傲游('.($matches[1] ?? '').')';
        }

        if (stripos($system, 'MSIE') > 0) {
            preg_match("/MSIE\s+([^;)]+)+/i", $system, $matches);

            return 'IE('.($matches[1] ?? '').')';
        }

        if (stripos($system, 'OPR') > 0) {
            preg_match("/OPR\/([\d\.]+)/", $system, $matches);

            return 'Opera('.($matches[1] ?? '').')';
        }

        // Edge浏览器
        if (stripos($system, 'Edge') > 0) {
            // win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
            preg_match("/Edge\/([\d\.]+)/", $system, $matches);

            return 'Edge('.($matches[1] ?? '').')';
        }

        // chrome
        if (stripos($system, 'Chrome') > 0) {
            preg_match("/Chrome\/([\d\.]+)/", $system, $matches);

            return 'Chrome('.($matches[1] ?? '').')';
        }

        // 苹果自带浏览器
        if (stripos($system, 'Safari') > 0) {
            preg_match("/Safari\/([\d\.]+)/", $system, $safari);

            return 'Safari('.($safari[1] ?? '').')';
        }

        if (stripos($system, 'rv:') > 0 && stripos($system, 'Gecko') > 0) {
            preg_match("/rv:([\d\.]+)/", $system, $matches);

            return 'IE('.($matches[1] ?? '').')';
        }

        if (stripos($system, 'iphone') > 0) {
            return 'iPhone()';
        }

        if (stripos($system, 'android') > 0) {
            return 'android()';
        }

        return '未知浏览器()';
    }
}

if (! function_exists('get_system')) {
    /**
     * 获取客户端操作系统信息.
     */
    function get_system(): string
    {
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
            return 'Windows ME';
        }

        if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
            return 'Windows Vista';
        }

        if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
            return 'Windows 7';
        }

        if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
            return 'Windows 8';
        }

        if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
            return 'Windows 10'; // 添加win10判断
        }

        if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
            return 'Windows XP';
        }

        if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
            return 'Windows 2000';
        }

        if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
            return 'Windows 32';
        }

        if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
            return 'SunOS';
        }

        if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
            return 'IBM OS/2';
        }

        if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent)) {
            return 'Macintosh';
        }

        if (preg_match('/linux/i', $agent)) {
            return 'Linux';
        }

        if (preg_match('/unix/i', $agent)) {
            return 'Unix';
        }

        if (preg_match('/PowerPC/i', $agent)) {
            return 'PowerPC';
        }

        if (preg_match('/AIX/i', $agent)) {
            return 'AIX';
        }

        if (preg_match('/HPUX/i', $agent)) {
            return 'HPUX';
        }

        if (preg_match('/NetBSD/i', $agent)) {
            return 'NetBSD';
        }

        if (preg_match('/BSD/i', $agent)) {
            return 'BSD';
        }

        if (preg_match('/OSF1/i', $agent)) {
            return 'OSF1';
        }

        if (preg_match('/IRIX/i', $agent)) {
            return 'IRIX';
        }

        if (preg_match('/FreeBSD/i', $agent)) {
            return 'FreeBSD';
        }

        if (preg_match('/teleport/i', $agent)) {
            return 'teleport';
        }

        if (preg_match('/flashget/i', $agent)) {
            return 'flashget';
        }

        if (preg_match('/webzip/i', $agent)) {
            return 'webzip';
        }

        if (preg_match('/offline/i', $agent)) {
            return 'offline';
        }

        if (preg_match('/iphone/i', $agent)) {
            return 'iphone';
        }

        if (preg_match('/Mac OS/i', $agent)) {
            // 获取苹果系统
            $data = substr($agent, stripos($agent, 'Mac OS'));
            $os = substr($data, 0, stripos($data, ';'));

            if (! $os) {
                return 'Mac OS';
            }

            return '未知操作系统';
        }

        if (str_contains($agent, 'iphone')) {
            return 'iPhone';
        }

        if (str_contains($agent, 'android')) {
            return 'android';
        }

        if (preg_match('/sogou/i', $agent)) {
            return '搜狗搜索';
        }

        if (preg_match('/bing/i', $agent)) {
            return '微软搜索';
        }

        if (preg_match('/baidu/i', $agent)) {
            return '百度搜索';
        }

        if (preg_match('/google/i', $agent)) {
            return '谷歌搜索';
        }

        if (preg_match('/360/i', $agent)) {
            return '360搜索';
        }

        if (preg_match('/soso/i', $agent)) {
            return '腾讯搜搜';
        }

        return '未知操作系统';
    }
}
