<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                             $id
 * @property string                          $phone      手机号
 * @property string                          $code       短信验证码
 * @property int                             $type       短信类型
 * @property string                          $start_time 开始时间
 * @property string                          $end_time   结束时间
 * @property string                          $ip
 * @property string|null                     $info       描述
 * @property int                             $status     短信验证码状态 默认为0 验证了为1
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhoneMsg whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PhoneMsg extends Model
{
    use DatetimeTrait;
    public const PHONE_LOGIN = 2; // 登录短信验证码
    public const PHONE_FORGET_PASSWORD = 3; // 忘记密码短信验证码
    public const PHONE_NOTICE = 4; // 通知类短信
    public const PHONE_EDIT_PASSWORD = 5; // 修改密码
    public const CODE_TIME = 5; // 验证码有效时间 5分钟
    public const STATUS_NOT_USED = 0; // 未使用
    public const STATUS_USED = 1; // 已使用

    public static array $messageMap = [
        self::PHONE_LOGIN => '登录短信验证码',
        self::PHONE_NOTICE => '通知类短信',
    ];

    protected $guarded = [];
}
