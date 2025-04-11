<?php

namespace App\Services;

use App\Enums\RouteEnum;
use App\Models\Router;

class RouteService
{
    /**
     * 获取完整请求地址
     */
    public function getRoutePath(RouteEnum $mobile_route_enum, array $params = []): string
    {
        $route = Router::query()->whereAlias($mobile_route_enum->value)->whereIsShow(Router::IS_SHOW_YES)->first();

        if (! $route instanceof Router) {
            return '';
        }

        $path = $route->h5_url;
        $set_data = [];

        foreach ($route->params as $key => $value) {
            if (isset($params[$key])) {
                $set_data[$key] = $params[$key];
            } else {
                $set_data[$key] = $value;
            }
        }

        return rtrim($path, '/').get_url_joiner($path).http_build_query($set_data);
    }
}
