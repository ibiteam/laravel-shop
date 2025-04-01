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

    // 是否在左侧菜单栏
    public const NOT_IS_LEFT_NAV = 0;
    public const IS_LEFT_NAV = 1;

    // 一级
    public const MODULE_SET = 'module.set'; // 设置
    public const MODULE_GOODS = 'module.goods'; // 商品
    public const MODULE_USER = 'module.user'; // 用户
    public const MODULE_ORDER = 'module.order'; // 订单
    public const MODULE_ARTICLE = 'module.article'; // 文章
    public const MODULE_TOOLS = 'module.tools'; // 工具
    public const MODULE_DATA = 'module.data'; // 数据

    // 二级 设置部分
    public const BASIC_SET_MANAGE = 'basic_set.manage'; // 基础设置
    public const MANAGE_SHOP_CONFIG_INDEX = 'manage.shop_config.index'; // 商店设置
    public const MANAGE_SHOP_CONFIG_UPDATE = 'manage.shop_config.update'; // 商店设置编辑
    public const MANAGE_ROUTER_CATEGORY_INDEX = 'manage.router_category.index'; // 访问地址分类
    public const MANAGE_ROUTER_CATEGORY_UPDATE = 'manage.router_category.update'; // 访问地址分类新增|编辑
    public const MANAGE_ROUTER_CATEGORY_DELETE = 'manage.router_category.delete'; // 访问地址分类删除
    public const MANAGE_ROUTER_INDEX = 'manage.router.index'; // 访问地址
    public const MANAGE_ROUTER_UPDATE = 'manage.router.update'; // 访问地址新增|编辑
    public const MANAGE_ROUTER_DELETE = 'manage.router.delete'; // 访问地址删除

    // 二级 商品部分
    public const GOODS_MANAGE = 'goods.manage'; // 商品管理
    public const MANAGE_GOODS_INDEX = 'manage.goods.index'; // 商品列表
    public const MANAGE_GOODS_UPDATE = 'manage.goods.update'; // 商品列表新增|编辑
    public const CATEGORY_MANAGE = 'category.manage'; // 分类管理
    public const MANAGE_CATEGORY_INDEX = 'manage.category.index'; // 商品分类
    public const MANAGE_CATEGORY_UPDATE = 'manage.category.update'; // 商品分类新增|编辑
    public const MANAGE_CATEGORY_DELETE = 'manage.category.delete'; // 商品分类删除

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
