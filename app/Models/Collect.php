<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $admin_user_id 管理员id
 * @property int                             $permission_id 菜单id（permissions表）
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdminUser|null $adminUser
 * @property-read \App\Models\Permission|null $permission
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collect query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collect whereAdminUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collect wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collect whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Collect extends Model
{
    use DatetimeTrait;

    protected $guarded = [];

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id', 'id');
    }
}
