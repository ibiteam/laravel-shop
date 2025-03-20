<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $user_id     用户ID
 * @property string                          $type        类型
 * @property string                          $source      来源
 * @property string                          $ip          IP
 * @property string                          $status      状态
 * @property string                          $description 描述
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\IpAddress|null $ipAddress
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLog whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLog whereUserId($value)
 *
 * @mixin \Eloquent
 */
class UserLog extends Model
{
    use DatetimeTrait;
    public const TYPE_LOGIN = 'login'; // 类型：登录
    public const TYPE_OPERATE = 'operate'; // 类型：操作
    public const STATUS_SUCCESS = 1; // 成功
    public const STATUS_FAILED = 0; // 失败

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function ipAddress(): BelongsTo
    {
        return $this->belongsTo(IpAddress::class, 'ip', 'ip');
    }
}
