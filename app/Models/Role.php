<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

/**
 * @property int                             $id
 * @property string                          $name          角色CODE
 * @property string                          $guard_name    分组名称
 * @property string                          $display_name  中文展示名称
 * @property int                             $is_super_role 是否是超级角色 1是 0否
 * @property int                             $is_show       是否显示 1是 0否
 * @property string|null                     $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ModelHasRole> $modelHasRole
 * @property-read int|null $model_has_role_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdminUser> $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereIsSuperRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
 *
 * @mixin \Eloquent
 */
class Role extends SpatieRole
{
    use DatetimeTrait;
    public const SHOW = 1; // 展示
    public const NOT_SHOW = 0; // 不展示

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $this->guardName();

        parent::__construct($attributes);
    }

    public function guardName()
    {
        return config('auth.manage.guard');
    }

    public function modelHasRole(): HasMany
    {
        return $this->hasMany(ModelHasRole::class, 'role_id', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name'] ?? $this->guardName()),
            'model',
            config('permission.table_names.model_has_roles'),
            app(PermissionRegistrar::class)->pivotRole,
            config('permission.column_names.model_morph_key')
        );
    }
}
