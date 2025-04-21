<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $user_id        用户ID
 * @property string|null $unionid        微信 unionid
 * @property string      $openid         微信 openid
 * @property string|null $nickname       昵称
 * @property string|null $avatar         头像
 * @property bool        $is_subscribe   是否关注
 * @property Carbon|null $subscribe_time 关注/取消关注时间
 * @property string      $language       用户语言
 * @property string      $remark         备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static Builder<static>|WechatUser newModelQuery()
 * @method static Builder<static>|WechatUser newQuery()
 * @method static Builder<static>|WechatUser query()
 * @method static Builder<static>|WechatUser whereAvatar($value)
 * @method static Builder<static>|WechatUser whereCreatedAt($value)
 * @method static Builder<static>|WechatUser whereId($value)
 * @method static Builder<static>|WechatUser whereIsSubscribe($value)
 * @method static Builder<static>|WechatUser whereLanguage($value)
 * @method static Builder<static>|WechatUser whereNickname($value)
 * @method static Builder<static>|WechatUser whereOpenid($value)
 * @method static Builder<static>|WechatUser whereRemark($value)
 * @method static Builder<static>|WechatUser whereSubscribeTime($value)
 * @method static Builder<static>|WechatUser whereUnionid($value)
 * @method static Builder<static>|WechatUser whereUpdatedAt($value)
 * @method static Builder<static>|WechatUser whereUserId($value)
 *
 * @mixin \Eloquent
 */
class WechatUser extends BaseModel
{


    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'subscribe_time' => 'datetime',
            'is_subscribe' => 'boolean',
        ];
    }
}
