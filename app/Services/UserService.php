<?php

namespace App\Services;

use App\Enums\CommonEnum;
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
    public function registerByPhone(int $phone, CommonEnum $source = CommonEnum::H5): User
    {
        $tmp_user_name = $this->generateUserName();

        return User::query()->create([
            'user_name' => $tmp_user_name,
            'password' => PasswordRuleService::generatePassword(),
            'nickname' => $tmp_user_name,
            'phone' => $phone,
            'avatar' => '',
            'register_ip' => get_request_ip(),
            'is_modify' => false,
            'source' => $source->value,
        ]);
    }

    /**
     * 根据用户名+手机号注册用户.
     */
    public function registerByUserNameAndPhone(array $params, CommonEnum $source = CommonEnum::PC): User
    {
        return User::query()->create([
            'user_name' => $params['account'],
            'password' => $params['password'],
            'nickname' => $params['account'],
            'phone' => $params['phone'],
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
        $access_token = $user->currentAccessToken();

        if (! $access_token instanceof PersonalAccessToken) {
            return $res;
        }

        return array_merge($res, [
            'is_login' => true,
            'token' => $token,
            'expires_at' => $access_token->expires_at->diffInSeconds(Carbon::now()),
        ]);
    }

    /**
     * 登录成功处理token.
     */
    public function loginSuccess(User $user, CommonEnum $source = CommonEnum::H5, string $token_name = 'api'): array
    {
        $now = Carbon::now();
        $future = $now->copy()->addDay();
        $token = $user->createToken($token_name, expiresAt: $future)->plainTextToken;

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
        return strtolower('lc_'.time().'_'.mt_rand(1000, 9999).'_'.mt_rand(10, 99));
    }
}
