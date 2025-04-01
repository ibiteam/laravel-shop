<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                             $id
 * @property int                             $admin_user_id 管理员id
 * @property int                             $permission_id 菜单id（permissions表）
 * @property int                             $is_show       是否显示：1显示 2隐藏
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdminUser|null $adminUser
 * @property-read \App\Models\Permission|null $permission
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRecord whereAdminUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRecord whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRecord wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessRecord whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AccessRecord extends Model
{
    use DatetimeTrait;

    public const IS_SHOW_YES = 1; // 显示
    public const IS_SHOW_NO = 0; // 不显示

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
