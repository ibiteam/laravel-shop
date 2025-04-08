<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $permission_id
 * @property int $role_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleHasPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleHasPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleHasPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleHasPermission wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleHasPermission whereRoleId($value)
 *
 * @mixin \Eloquent
 */
class RoleHasPermission extends Model
{
    use DatetimeTrait;
    protected $guarded = [];
}
