<?php

namespace App\Http\Middleware\Manage;

use App\Enums\CustomCodeEnum;
use App\Exceptions\BusinessException;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionCheck
{
    /**
     * @throws BusinessException
     */
    public function handle(Request $request, \Closure $next, $permission = null)
    {
        $admin_user = Auth::guard(config('auth.manage.guard'))->user();

        if (! ($admin_user instanceof AdminUser)) {
            throw new BusinessException('用户未登录或用户异常', CustomCodeEnum::UNAUTHORIZED);
        }

        if ($permission === null) {
            // 不传权限, 则取当前路由名的权限
            $permission = $request->route()->getName();

            if (! $permission) {
                throw new BusinessException('用户无权限访问', CustomCodeEnum::FORBIDDEN);
            }
        }
        $permissions = is_array($permission) ? $permission : explode('|', $permission);

        if (! $admin_user->canAny($permissions)) {
            throw new BusinessException('用户无权限访问', CustomCodeEnum::FORBIDDEN);
        }

        return $next($request);
    }
}
