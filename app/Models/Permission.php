<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\PermissionRegistrar;

class Permission extends SpatiePermission
{
    use DatetimeTrait;
    public const NOT_IS_LEFT_NAV = 0;
    public const IS_LEFT_NAV = 1;
    public const MANAGE_SETTINGS = 'manage.settings'; // 设置
    public const MANAGE_STORE = 'manage.store'; // 店铺
    public const MANAGE_USER = 'manage.user'; // 用户
    public const MANAGE_ARTICLE = 'manage.article'; // 文章
    public const MANAGE_TOOLS = 'manage.tools'; // 工具
    public const MANAGE_DATA = 'manage.data'; // 数据
    public const MANAGE_PRODUCT = 'manage.product'; // 产品

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $this->guardName();

        parent::__construct($attributes);
    }

    public function guardName()
    {
        return config('auth.manage.guard');
    }

    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name'] ?? $this->guardName()),
            'model',
            config('permission.table_names.model_has_permissions'),
            app(PermissionRegistrar::class)->pivotPermission,
            config('permission.column_names.model_morph_key')
        );
    }

    /**
     * 复写此方法 用户将角色禁用后 将对应角色下的权限失效.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions'),
            app(PermissionRegistrar::class)->pivotPermission,
            app(PermissionRegistrar::class)->pivotRole
        )->where(config('permission.table_names.roles').'.is_show', Role::SHOW);
    }
}
