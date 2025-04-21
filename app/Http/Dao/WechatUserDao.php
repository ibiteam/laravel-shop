<?php

namespace App\Http\Dao;

use App\Models\WechatUser;

class WechatUserDao
{
    /**
     * 根据openid获取用户信息.
     */
    public function getInfoByOpenid(string $openid): ?WechatUser
    {
        return WechatUser::query()->whereOpenid($openid)->first();
    }

    /**
     * 更新用户id.
     */
    public function updateUserIdByWechatUser(WechatUser $wechat_user, int $user_id): void
    {
        if ($wechat_user->user_id === $user_id) {
            return;
        }
        WechatUser::query()->whereUserId($user_id)->update(['user_id' => 0]);

        $wechat_user->update(['user_id' => $user_id]);
    }
}
