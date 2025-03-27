<?php

namespace App\Http\Dao;

use App\Models\AdminUser;

class AdminOperationLogDao
{
    /**
     * 添加后台操作日志.
     */
    public function addOperationLogByAdminUser(AdminUser $admin_user, string $description, int $type): void
    {
        $admin_user->operationLog()->create([
            'description' => $description,
            'type' => $type,
            'ip' => get_request_ip(),
        ]);
    }
}
