<?php

namespace App\Services;

use App\Enums\RouterEnum;
use App\Models\Router;

class RouterService
{
    /**
     * 获取完整请求地址
     */
    public function getRouterPath(RouterEnum $router_enum, array $params = []): string
    {
        $router = Router::query()->whereAlias($router_enum->value)->whereIsShow(Router::IS_SHOW_YES)->first();

        if (! $router instanceof Router) {
            return '';
        }

        $path = rtrim(config('host.vue_app_url'), '/').'/'.ltrim($router->h5_url, '/');
        $set_data = [];

        foreach ($router->params as $key => $value) {
            if (isset($params[$key])) {
                $set_data[$key] = $params[$key];
            } else {
                $set_data[$key] = $value;
            }
        }

        return rtrim($path, '/').get_url_joiner($path).http_build_query($set_data);
    }
}
