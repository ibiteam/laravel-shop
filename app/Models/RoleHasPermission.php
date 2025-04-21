<?php

namespace App\Models;




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
class RoleHasPermission extends BaseModel
{

    protected $guarded = [];
}
