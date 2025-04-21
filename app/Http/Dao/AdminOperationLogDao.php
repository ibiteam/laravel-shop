<?php

namespace App\Http\Dao;

use App\Models\AdminOperationLog;

class AdminOperationLogDao
{
    /**
     * 添加后台操作日志.
     */
    public function addOperationLogByAdminUser(int $admin_user_id, string $description, int $type): AdminOperationLog
    {
        return AdminOperationLog::create([
            'description' => $description,
            'admin_user_id' => $admin_user_id,
            'type' => $type,
            'ip' => get_request_ip(),
        ]);
    }
}
