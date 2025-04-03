<?php

namespace App\Http\Middleware\Manage;

use App\Enums\ConstantEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\AccessRecordDao;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccessRecord
{
    /**
     * @throws BusinessException
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $admin_user = Auth::guard(config('auth.manage.guard'))->user();

        if (! ($admin_user instanceof AdminUser)) {
            throw new BusinessException('用户未登录或用户异常', ConstantEnum::UNAUTHORIZED);
        }

        $route_name = $request->route()->getName();

        app(AccessRecordDao::class)->updateOrCreate($admin_user->id, $route_name);

        return $next($request);
    }
}
