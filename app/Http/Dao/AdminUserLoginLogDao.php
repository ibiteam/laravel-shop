<?php

namespace App\Http\Dao;

use App\Models\AdminUser;

class AdminUserLoginLogDao
{
    /**
     * 添加后台登录日志.
     */
    public function addLoginLogByAdminUser(AdminUser $admin_user, string $type, int $status, string $description): void
    {
        $admin_user->loginLog()->create([
            'type' => $type,
            'status' => $status,
            'description' => $description,
            'ip' => get_request_ip(),
        ]);
    }
}
