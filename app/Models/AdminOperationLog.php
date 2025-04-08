<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                             $id
 * @property int                             $admin_user_id 管理员id
 * @property string|null                     $description   操作描述
 * @property int                             $type          类型
 * @property string                          $ip            操作IP
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdminUser $adminUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ModelHasRole> $modelHasRole
 * @property-read int|null $model_has_role_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereAdminUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminOperationLog whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AdminOperationLog extends Model
{
    use DatetimeTrait;
    public const TYPE_STORE = 1; // 新增
    public const TYPE_UPDATE = 2; // 更新
    public const TYPE_DESTROY = 3; // 删除

    protected $guarded = [];

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id', 'id');
    }

    public function modelHasRole(): HasMany
    {
        return $this->hasMany(ModelHasRole::class, 'model_id', 'admin_user_id');
    }
}
