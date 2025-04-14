<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property int         $user_id        用户ID
 * @property string|null $unionid        微信 unionid
 * @property string      $openid         微信 openid
 * @property string|null $nickname       昵称
 * @property string|null $avatar         头像
 * @property int         $is_subscribe   是否关注
 * @property string      $subscribe_time 关注/取消关注时间
 * @property string      $language       用户语言
 * @property string      $remark         备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereIsSubscribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereSubscribeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WechatUser whereUserId($value)
 *
 * @mixin \Eloquent
 */
class WechatUser extends Model
{
    use DatetimeTrait;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
