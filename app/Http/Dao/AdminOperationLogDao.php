<?php

namespace App\Http\Dao;

use App\Models\AdminOperationLog;
use App\Models\AdminUser;
use App\Models\SellerEnter;

class AdminOperationLogDao
{
    /**
     * 添加后台操作日志.
     */
    public function addOperationLogByAdminUser(AdminUser $admin_user, string $description, int $type, ?string $table_name, int $table_id): void
    {
        $admin_user->operationLog()->create([
            'description' => $description,
            'type' => $type,
            'table' => $table_name,
            'table_id' => $table_id,
            'ip' => get_request_ip(),
        ]);
    }

    /**
     * 获取商家入驻审核记录.
     */
    public function getSellerEnterCheckByLog(int $seller_enter_id)
    {
        return AdminOperationLog::query()
            ->where('table', (new SellerEnter())->getTable())
            ->where('table_id', $seller_enter_id)
            ->where('type', AdminOperationLog::SELLER_ENTER_CHECK)
            ->orderByDesc('id')
            ->get()->map(function (AdminOperationLog $admin_operation_log) {
                return [
                    'id' => $admin_operation_log->id,
                    'description' => $admin_operation_log->description,
                    'admin_user_name' => $admin_operation_log->adminUser?->user_name,
                ];
            });
    }
}
