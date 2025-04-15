<?php

namespace App\Services;

use App\Enums\CacheNameEnum;
use App\Http\Dao\AdminUserLoginLogDao;
use App\Models\AdminUser;
use App\Models\AdminUserLoginLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class AdminUserService
{
    /**
     * 登录成功处理token.
     */
    public function loginSuccess(AdminUser $admin_user): array
    {
        $now = Carbon::now();
        $future = $now->copy()->addDay();

        $token = $admin_user->createToken('manage', expiresAt: $future)->plainTextToken;

        app(AdminUserLoginLogDao::class)->addLoginLogByAdminUser(
            $admin_user,
            AdminUserLoginLog::TYPE_PASSWORD,
            AdminUserLoginLog::STATUS_SUCCESS,
            '账号密码登录成功~'
        );

        Cache::forget(CacheNameEnum::ADMIN_PERMISSION_MENUS->value.'_'.$admin_user->id);

        return [
            'token' => $token,
            'expires_at' => $now->diffInSeconds($future),
        ];
    }
}
