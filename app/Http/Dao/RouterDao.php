<?php

namespace App\Http\Dao;

use App\Models\Router;
use App\Services\MobileRouterService;
use App\Utils\Constant;

class RouterDao
{
    /**
     * 底部菜单页，url链接下拉数据中排除（主页）.
     *
     * @var array
     */
    public static $bottom_menu_page = [
        Router::HOME, // 首页
        Router::ZIXUN_INDEX, // 指数
        Router::CATEGORY, // 分类
        Router::HOME_APP_MESSAGE, // 消息
        Router::PROMOTE, // 分销
        Router::CART, // 购物车
        Router::MY_ORDER, // 订单
        Router::PUBLIC_PAGE, // 公共页面主页
    ];

    /**
     * @return string
     */
    public function model()
    {
        return Router::class;
    }

    /**
     * @return mixed[]
     */
    public static function mustAppUrlNotAll()
    {
        $router = Router::whereIsShow(Router::IS_SHOW)
            ->where('app_url', '<>', '')
            ->get()->map(function ($item) {
                $item->params = json_decode($item->params);

                return $item;
            })->toArray();

        return $router;
    }

    /**
     * 移动端url路由链接（所有显示的数据）.
     *
     * @param  array $alias_arr
     * @return array
     */
    public static function router($alias_arr = [])
    {
        $query = Router::whereIsShow(Router::IS_SHOW);

        if ($alias_arr) {
            $query->whereIn('alias', $alias_arr);
        }

        return $query->select('name', 'alias', 'params', 'app_url', 'harmony_url', 'h5_url', 'mini_url', 'ios_is_open', 'android_is_open', 'harmony_is_open', 'mini_is_open')
            ->get()->map(function (Router $item) {
                $item->params = json_decode($item->params);

                return $item;
            })->toArray();
    }

    public static function allShow($alias_arr = [])
    {
        $query = Router::whereIsShow(Router::IS_SHOW);

        if ($alias_arr) {
            $query->whereIn('alias', $alias_arr);
        }

        return $query->get()->map(function (Router $item) {
            $item->params = json_decode($item->params);

            return $item;
        })->toArray();
    }

    /**
     * 通过别名获取路由信息.
     *
     * @return Router|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getRouteInfoByAlias(string $alias)
    {
        $router = Router::whereIsShow(Router::IS_SHOW)->whereAlias($alias)->first();

        if (! $router) {
            return null;
        }
        $final_app_mini_url = self::getFinalAppMiniUrl($router);
        $router->app_url = $final_app_mini_url['app_url'];
        $router->mini_url = $final_app_mini_url['mini_url'];
        $router->params = json_decode($router->params, true);

        return $router;
    }

    /**
     * 根据不同系统|不同来源。查看是否开启，返回对应的地址，未开启返回空.
     *
     * @param        $system
     * @return array
     */
    private static function getFinalAppMiniUrl(Router $router)
    {
        $app_url = '';
        $mini_url = '';
        $resource = sourcePort();

        if ($resource == MobileRouterService::SOURCE_APP) {
            if (is_ios_request() && $router['ios_is_open'] == Constant::ONE) {
                $app_url = $router['app_url'];
            } elseif (is_android_request() && $router['android_is_open'] == Constant::ONE) {
                $app_url = $router['app_url'];
            } elseif (is_harmony_request() && $router['harmony_is_open'] == Constant::ONE) {
                $app_url = $router['harmony_url'];
            }
        } elseif ($resource == MobileRouterService::SOURCE_MINI) {
            if ($router['mini_is_open'] == Constant::ONE) {
                $mini_url = $router['mini_url'];
            }
        }

        return [
            'app_url' => $app_url,
            'mini_url' => $mini_url,
        ];
    }
}
