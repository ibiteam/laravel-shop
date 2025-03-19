<?php

namespace App\Http\Middleware\Manage;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as IlluminateAuthenticate;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate extends IlluminateAuthenticate
{
    public function __construct(Auth $auth)
    {
        parent::__construct($auth);
        // 重定向到后台登录页面
        parent::redirectUsing(fn () => route('manage.login.form'));
    }

    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, [config('auth.manage.guard')]);

        return $next($request);
    }
}
