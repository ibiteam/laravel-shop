<?php

namespace App\Services;

class ManageRouteService
{
    private const MANAGE_HOME = 'manage/home';

    /**
     * 管理后台首页.
     */
    public static function manageHome(): string
    {
        return self::getHost().'/'.self::MANAGE_HOME;
    }

    private static function getHost(): string
    {
        return trim(config('app.url'), '/');
    }
}
