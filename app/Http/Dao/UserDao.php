<?php

namespace App\Http\Dao;

use App\Models\User;

class UserDao
{
    /**
     * 根据手机号获取用户信息.
     */
    public function getInfoByPhone(int $phone): ?User
    {
        return User::query()->wherePhone($phone)->first();
    }
}
