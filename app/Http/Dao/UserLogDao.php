<?php

namespace App\Http\Dao;

use App\Enums\RefererEnum;
use App\Models\User;
use App\Models\UserLog;

class UserLogDao
{
    /**
     * 添加用户日志.
     */
    public function addLog(User $user, string $type = '', RefererEnum $common_enum = RefererEnum::H5, string $description = ''): void
    {
        $user->userLogs()->create([
            'type' => $type,
            'source' => $common_enum->value,
            'ip' => get_request_ip(),
            'status' => UserLog::STATUS_SUCCESS,
            'description' => $description,
        ]);
    }
}
