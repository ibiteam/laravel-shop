<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as IlluminateAuthenticate;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;

class Authenticate extends IlluminateAuthenticate
{
    public function __construct(Auth $auth)
    {
        parent::__construct($auth);
        // 重定向到首页
        parent::redirectUsing(fn (Request $request) => '/');
    }

    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, [config('auth.api.guard')]);

        return $next($request);
    }
}
