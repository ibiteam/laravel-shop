<?php

namespace App\Http\Dao;

use App\Models\AdminUser;
use App\Models\Order;
use App\Models\OrderLog;

class OrderLogDao
{
    /**
     * 管理员操作订单记录日志.
     */
    public function storeByAdminUser(AdminUser $admin_user, Order $order, string $description): void
    {
        $admin_user->orderLog()->create([
            'order_id' => $order->id,
            'type' => OrderLog::TYPE_USER,
            'order_status' => $order->order_status,
            'pay_status' => $order->pay_status,
            'ship_status' => $order->ship_status,
            'comment' => $description,
        ]);
    }
}
