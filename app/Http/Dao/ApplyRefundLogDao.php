<?php

namespace App\Http\Dao;

use App\Models\ApplyRefundLog;

class ApplyRefundLogDao
{
    /**
     * 添加记录
     */
    public function addLog($apply_refund_id, $action_name, $action, $type)
    {
        return ApplyRefundLog::query()->create([
            'apply_refund_id' => $apply_refund_id,
            'action_name' => $action_name,
            'action' => $action,
            'type' => $type,
        ]);
    }

}
