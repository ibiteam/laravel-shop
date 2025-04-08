<?php

namespace App\Http\Dao;

use App\Models\Order;

class OrderDao
{
    public function getInfoByNo(string $no, int $user_id): ?Order
    {
        return Order::query()->whereUserId($user_id)->whereNo($no)->first();
    }
}
