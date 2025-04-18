<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property int         $id
 * @property string      $user_name   用户名
 * @property string      $password    密码
 * @property string      $nickname    昵称
 * @property string      $phone       手机号
 * @property string|null $avatar      头像
 * @property string      $register_ip 注册IP
 * @property bool        $is_modify   是否已修改用户名
 * @property int         $integral    积分总数
 * @property string      $source      来源
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, OrderEvaluate> $evaluates
 * @property-read int|null $evaluates_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, OrderLog> $orderLog
 * @property-read int|null $order_log_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read Collection<int, UserLog> $userLogs
 * @property-read int|null $user_logs_count
 *
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereAvatar($value)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereIntegral($value)
 * @method static Builder<static>|User whereIsModify($value)
 * @method static Builder<static>|User whereNickname($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User wherePhone($value)
 * @method static Builder<static>|User whereRegisterIp($value)
 * @method static Builder<static>|User whereSource($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User whereUserName($value)
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use DatetimeTrait, HasApiTokens, Notifiable;

    // 修改过用户名
    public const IS_MODIFY_YES = 1;

    protected $guarded = [];

    protected $hidden = ['password'];

    public function orderLog(): MorphMany
    {
        return $this->morphMany(OrderLog::class, 'operateType', 'operate_type', 'operate_type_id');
    }

    public function userLogs(): HasMany
    {
        return $this->hasMany(UserLog::class, 'user_id', 'id');
    }

    public function evaluates(): HasMany
    {
        return $this->hasMany(OrderEvaluate::class, 'user_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_modify' => 'boolean',
        ];
    }
}
