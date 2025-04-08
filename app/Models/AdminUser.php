<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int         $id
 * @property string      $user_name  登录用户名
 * @property string      $password   登录密码
 * @property string|null $nickname   昵称
 * @property string|null $avatar     头像
 * @property string      $phone      手机号
 * @property string|null $job_no     工号
 * @property int         $status     状态：1启用 0禁用
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\AdminUserLoginLog|null $lastLoginLog
 * @property-read Collection<int, \App\Models\AdminUserLoginLog> $loginLog
 * @property-read int|null $login_log_count
 * @property-read Collection<int, \App\Models\ModelHasRole> $modelHasRole
 * @property-read int|null $model_has_role_count
 * @property-read Collection<int, \App\Models\AdminOperationLog> $operationLog
 * @property-read int|null $operation_log_count
 * @property-read Collection<int, \App\Models\OrderLog> $orderLog
 * @property-read int|null $order_log_count
 * @property-read Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static Builder<static>|AdminUser newModelQuery()
 * @method static Builder<static>|AdminUser newQuery()
 * @method static Builder<static>|AdminUser permission($permissions, $without = false)
 * @method static Builder<static>|AdminUser query()
 * @method static Builder<static>|AdminUser role($roles, $guard = null, $without = false)
 * @method static Builder<static>|AdminUser whereAvatar($value)
 * @method static Builder<static>|AdminUser whereCreatedAt($value)
 * @method static Builder<static>|AdminUser whereId($value)
 * @method static Builder<static>|AdminUser whereJobNo($value)
 * @method static Builder<static>|AdminUser whereNickname($value)
 * @method static Builder<static>|AdminUser wherePassword($value)
 * @method static Builder<static>|AdminUser wherePhone($value)
 * @method static Builder<static>|AdminUser whereStatus($value)
 * @method static Builder<static>|AdminUser whereUpdatedAt($value)
 * @method static Builder<static>|AdminUser whereUserName($value)
 * @method static Builder<static>|AdminUser withoutPermission($permissions)
 * @method static Builder<static>|AdminUser withoutRole($roles, $guard = null)
 *
 * @mixin \Eloquent
 */
class AdminUser extends Authenticatable
{
    use DatetimeTrait;
    use HasApiTokens;
    use HasRoles;
    public const STATUS_ENABLE = 1; // 启用
    public const STATUS_DISABLE = 0; // 禁用
    protected $guarded = [];

    protected $hidden = ['password'];

    public function modelHasRole(): HasMany
    {
        return $this->hasMany(ModelHasRole::class, 'model_id', 'id');
    }

    public function orderLog(): MorphMany
    {
        return $this->morphMany(OrderLog::class, 'operateType');
    }

    public function loginLog(): HasMany
    {
        return $this->hasMany(AdminUserLoginLog::class, 'admin_user_id', 'id');
    }

    public function lastLoginLog(): HasOne
    {
        return $this->hasOne(AdminUserLoginLog::class, 'admin_user_id', 'id')->orderBy('id', 'desc');
    }

    public function operationLog(): HasMany
    {
        return $this->hasMany(AdminOperationLog::class, 'admin_user_id', 'id');
    }

    /**
     * 生成头像文字.
     */
    public function generateAvatarText(): string
    {
        $name = $this->getRawOriginal('user_name');

        return mb_substr($name, -2, null, 'UTF-8');
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $value ?: '',
        );
    }
}
