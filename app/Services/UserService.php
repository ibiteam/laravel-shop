<?php

namespace App\Services;

use App\Enums\CommonEnum;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Support\Carbon;

class UserService
{
    /**
     * 根据手机号注册用户.
     */
    public function registerByPhone(int $phone, CommonEnum $source = CommonEnum::H5): User
    {
        $tmp_user_name = $this->generateUserName();

        return User::query()->create([
            'user_name' => $tmp_user_name,
            'password' => $tmp_user_name.time(),
            'nickname' => $tmp_user_name,
            'phone' => $phone,
            'avatar' => '',
            'register_ip' => get_request_ip(),
            'is_modify' => false,
            'source' => $source->value,
        ]);
    }

    /**
     * 登录成功处理token.
     */
    public function loginSuccess(User $user, CommonEnum $source = CommonEnum::H5): array
    {
        $now = Carbon::now();
        $future = $now->copy()->addDay();
        $token = $user->createToken('api', expiresAt: $future)->plainTextToken;

        $user_log = new UserLog;
        $user_log->user_id = $user->id;
        $user_log->type = UserLog::TYPE_LOGIN;
        $user_log->source = $source->value;
        $user_log->ip = get_request_ip();
        $user_log->status = UserLog::STATUS_SUCCESS;
        $user_log->description = '登录成功';
        $user_log->save();

        return [
            'token' => $token,
            'expires_at' => $now->diffInSeconds($future),
        ];
    }

    /**
     * 系统生成用户名.
     */
    private function generateUserName(): string
    {
        return strtolower('lc_'.time().'_'.mt_rand(1000, 9999).'_'.mt_rand(10, 99));
    }
}
