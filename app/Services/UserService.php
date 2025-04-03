<?php

namespace App\Services;

use App\Enums\RefererEnum;
use App\Exceptions\BusinessException;
use App\Http\Dao\UserLogDao;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class UserService
{
    /**
     * 根据手机号注册用户.
     */
    public function registerByPhone(int $phone, RefererEnum $source = RefererEnum::H5): User
    {
        $tmp_user_name = $this->generateUserName();

        return User::query()->create([
            'user_name' => $tmp_user_name,
            'password' => md5($tmp_user_name.time()),
            'nickname' => $tmp_user_name,
            'phone' => $phone,
            'avatar' => '',
            'register_ip' => get_request_ip(),
            'is_modify' => false,
            'source' => $source->value,
        ]);
    }

    /**
     * 检测用户是否登录.
     */
    public function checkIsLogin(?User $user, string $token): array
    {
        $res = [
            'is_login' => false,
            'token' => '',
            'expires_at' => 0,
        ];

        try {
            if (! $user instanceof User) {
                throw new BusinessException('用户未登录');
            }
            $access_token = $user->currentAccessToken();

            if (! $access_token instanceof PersonalAccessToken) {
                throw new BusinessException('用户未登录');
            }

            return array_merge($res, [
                'is_login' => true,
                'token' => $token,
                'expires_at' => $access_token->expires_at->diffInSeconds(Carbon::now()),
            ]);
        } catch (\Throwable) {
            return $res;
        }
    }

    /**
     * 登录成功处理token.
     */
    public function loginSuccess(User $user, RefererEnum $source = RefererEnum::H5): array
    {
        $now = Carbon::now();
        $future = $now->copy()->addDay();
        $token = $user->createToken('api', expiresAt: $future)->plainTextToken;

        app(UserLogDao::class)->addLog($user, UserLog::TYPE_LOGIN, $source, '登录成功');

        return [
            'token' => $token,
            'expires_at' => $now->diffInSeconds($future),
        ];
    }

    /**
     * 退出登录.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * 系统生成用户名.
     */
    private function generateUserName(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $characters_length = strlen($characters);

        do {
            $random_string = '';

            for ($i = 0; $i < 15; $i++) {
                $random_string .= $characters[rand(0, $characters_length - 1)];
            }

            $user_name = $random_string.'_'.mt_rand(100000, 999999);
        } while (User::query()->whereUserName($user_name)->exists());

        return $user_name;
    }
}
