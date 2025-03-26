<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int                             $id
 * @property int                             $seller_id   商家id
 * @property string                          $user_name   用户名
 * @property string                          $password    密码
 * @property string                          $nickname    昵称
 * @property string                          $phone       手机号
 * @property string|null                     $avatar      头像
 * @property string                          $register_ip 注册IP
 * @property bool                            $is_modify   是否已修改用户名
 * @property string                          $source      来源
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\SellerEnter|null $sellerEnter
 * @property-read \App\Models\SellerShop|null $sellerShop
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserLog> $userLogs
 * @property-read int|null $user_logs_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsModify($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRegisterIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUserName($value)
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use DatetimeTrait, HasApiTokens, Notifiable;

    public const HOME_ACCESS_TOKEN_NAME = 'home'; // web端登录token名称

    protected $guarded = [];

    protected $hidden = ['password'];

    public function userLogs(): HasMany
    {
        return $this->hasMany(UserLog::class, 'user_id', 'id');
    }

    public function sellerEnter(): HasOne
    {
        return $this->hasOne(SellerEnter::class, 'user_id', 'id');
    }

    public function sellerShop(): HasOne
    {
        return $this->hasOne(SellerShop::class, 'seller_id', 'seller_id');
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_modify' => 'boolean',
        ];
    }
}
