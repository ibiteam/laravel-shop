<?php

namespace App\Services;

use App\Enums\RouterEnum;
use App\Models\Router;

class RouterService
{
    /**
     * 获取完整请求地址
     */
    public function getRouterPath($router_alias, array $params = []): string
    {
        if (!RouterEnum::formSource($router_alias)) {
            return '';
        }

        $router = Router::query()->whereAlias($router_alias)->whereIsShow(Router::IS_SHOW_YES)->first();

        if (! $router instanceof Router) {
            return '';
        }

        $path = rtrim(config('host.vue_app_url'), '/').'/'.ltrim($router->h5_url, '/');

        foreach ($params as $k => $v) {
            $search = "/:{$k}";

            if (str_contains($path, $search)) {
                $path = str_replace($search, "/{$v}", $path);
            }
        }

        $set_data = [];

        if ($router->params) {
            foreach ($router->params as $key => $value) {
                $set_data[$key] = $params[$key] ?? $value;
            }
        }

        if ($set_data) {
            return rtrim($path, '/').get_url_joiner($path).http_build_query($set_data);
        }

        return rtrim($path, '/');
    }
}
