<?php

use App\Http\Dao\ShopConfigDao;
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
