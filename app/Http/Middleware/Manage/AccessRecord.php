<?php

namespace App\Http\Middleware\Manage;

use App\Http\Dao\AccessRecordDao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccessRecord
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $admin_user_id = Auth::guard(config('auth.manage.guard'))->user()->id ?? 0;

        $route_name = $request->route()->getName();

        app(AccessRecordDao::class)->updateOrCreate($admin_user_id, $route_name);

        return $next($request);
    }
}
