<?php

namespace App\Http\Dao;

use App\Models\User;
use App\Models\UserIntegral;

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
     * 减少用户积分，并增加积分使用记录.
     */
    public function decrementIntegralByDoneOrder(User $user, int $integral, string $description): void
    {
        $user->decrement('integral', $integral);

        UserIntegral::query()->create([
            'user_id' => $user->id,
            'number' => $integral,
            'type' => UserIntegral::TYPE_DECREMENT,
            'desc' => $description,
        ]);
    }

    /**
     * 增加用户积分，并增加积分使用记录.
     */
    public function incrementIntegralByDoneOrder(User $user, int $integral, string $description): void
    {
        $user->increment('integral', $integral);

        UserIntegral::query()->create([
            'user_id' => $user->id,
            'number' => $integral,
            'type' => UserIntegral::TYPE_INCREMENT,
            'desc' => $description,
        ]);
    }
}
