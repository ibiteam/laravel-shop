<?php

namespace App\Http\Middleware\Manage;

use Closure;
use Illuminate\Session\Middleware\StartSession;

class CustomStartSession extends StartSession
{
    public function handle($request, Closure $next): mixed
    {
        config(['session.cookie' => config('session.manage_cookie')]);

        return parent::handle($request, $next);
    }
}
