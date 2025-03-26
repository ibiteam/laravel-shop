<?php

namespace App\Models;

use App\Traits\DatetimeTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\PermissionRegistrar;

/**
 * @property int                             $id
 * @property int                             $parent_id    父级ID
 * @property string                          $name         权限CODE
 * @property string                          $guard_name   分组名称
 * @property string                          $display_name 中文展示名称
 * @property string|null                     $icon         图标
 * @property int                             $sort         排序:value越大越靠前
 * @property int                             $is_left_nav  是否在左侧导航栏 1是 0否
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdminUser> $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereIsLeftNav($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutRole($roles, $guard = null)
 *
 * @mixin \Eloquent
 */
class Permission extends SpatiePermission
{
    use DatetimeTrait;
    public const NOT_IS_LEFT_NAV = 0;
    public const IS_LEFT_NAV = 1;
    public const MANAGE_SETTINGS = 'manage.settings'; // 设置
    public const MANAGE_GOODS = 'manage.goods'; // 商品
    public const MANAGE_GOODS_SETTINGS = 'manage.goods.settings'; // 商品管理
    public const MANAGE_GOODS_INDEX = 'manage.goods.index'; // 商品列表
    public const MANAGE_GOODS_CATEGORY = 'manage.goods.category'; // 商品分类
    public const MANAGE_GOODS_BRAND = 'manage.goods.brand'; // 商品品牌
    public const MANAGE_GOODS_LABELS = 'manage.goods.label'; // 商品标签
    public const MANAGE_GOODS_GUARANTEE = 'manage.goods.guarantee'; // 商品保障
    public const MANAGE_GOODS_SKU_TEMPLATE = 'manage.goods.sku_template'; // 商品SKU模板

    public const MANAGE_USER = 'manage.user'; // 用户
    public const MANAGE_ORDER = 'manage.order'; // 用户
    public const MANAGE_ARTICLE = 'manage.article'; // 文章
    public const MANAGE_TOOLS = 'manage.tools'; // 工具
    public const MANAGE_DATA = 'manage.data'; // 数据


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
