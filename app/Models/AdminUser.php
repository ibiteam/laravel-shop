<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int                             $id
 * @property string                          $user_name  登录用户名
 * @property string                          $phone      手机号
 * @property string                          $avatar     头像
 * @property string                          $password   登录密码
 * @property string                          $nickname   昵称
 * @property int                             $status     状态：1启用 0禁用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdminUserLoginLog|null $lastLoginLog
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdminUserLoginLog> $loginLog
 * @property-read int|null $login_log_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereUserName($value)
 *
 * @mixin \Eloquent
 */
class AdminUser extends Authenticatable
{
    use DatetimeTrait;
    public const STATUS_ENABLE = 1; // 启用
    public const STATUS_DISABLE = 0; // 禁用
    protected $guarded = [];

    protected $hidden = ['password'];

    public function loginLog(): HasMany
    {
        return $this->hasMany(AdminUserLoginLog::class, 'admin_user_id', 'id');
    }

    public function lastLoginLog(): HasOne
    {
        return $this->hasOne(AdminUserLoginLog::class, 'admin_user_id', 'id')->orderBy('id', 'desc');
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcrypt($value),
        );
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $value ?: '',
        );
    }
}
