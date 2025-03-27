<?php

namespace App\Services;

class ManageRouteService
{
    private const MANAGE_HOME = 'manage/home';
    private const MANAGE_LOGIN = 'manage/login';

    /**
     * 管理后台首页.
     */
    public static function manageHome(): string
    {
        return self::getHost().'/'.self::MANAGE_HOME;
    }

    /**
     * 管理后台登录.
     * @return string
     */
    public static function manageLogin(): string
    {
        return self::getHost().'/'.self::MANAGE_LOGIN;
    }

    private static function getHost(): string
    {
        return trim(config('app.url'), '/');
    }
}
