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

    /**
     * 根据用户名获取用户信息.
     */
    public function getInfoByUserName(string $user_name): ?User
    {
        return User::query()->whereUserName($user_name)->first();
    }

    /**
     * 校验字符串不能用特殊字符 和空格
     */
    public function checkUserName(string $user_name): bool
    {
        $str = str_replace('_', '', $user_name);

        return preg_match('/([[:space:]]|[[:punct:]])+/U', $str) === 1;
    }
}
