<?php

namespace App\Models;


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
    public const MODULE_MARKETING = 'module.marketing'; // 营销

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
    public const MANAGE_APP_SERVICE_CONFIG_INDEX = 'manage.app_service_config.index'; // 外部服务
    public const MANAGE_APP_SERVICE_CONFIG_UPDATE = 'manage.app_service_config.update'; // 外部服务编辑
    public const MANAGE_APP_SERVICE_LOG_INDEX = 'manage.app_service_config_log.index'; // 外部服务日志
    public const PERMISSION_MANAGE = 'permission.manage'; // 权限管理
    public const MANAGE_ADMIN_USER_INDEX = 'manage.admin_user.index'; // 管理员列表
    public const MANAGE_ADMIN_USER_UPDATE = 'manage.admin_user.update'; // 管理员新增|编辑
    public const MANAGE_PERMISSION_INDEX = 'manage.permission.index'; // 权限菜单
    public const MANAGE_PERMISSION_UPDATE = 'manage.permission.update'; // 权限菜单编辑
    public const MANAGE_ROLE_INDEX = 'manage.role.index'; // 角色管理
    public const MANAGE_ROLE_UPDATE = 'manage.role.update'; // 角色新增|编辑
    public const MANAGE_ROLE_DELETE = 'manage.role.delete'; // 角色删除
    public const MANAGE_ADMIN_OPERATION_LOG_INDEX = 'manage.admin_operation_log.index'; // 管理员日志
    public const BASIC_PAYMENT_MANAGE = 'basic.payment.manage'; // 支付管理
    public const MANAGE_PAYMENT_INDEX = 'manage.payment.index'; // 支付方式列表
    public const MANAGE_PAYMENT_UPDATE = 'manage.payment.update'; // 支付方式编辑
    public const MANAGE_TRANSACTION_INDEX = 'manage.transaction.index'; // 交易记录
    public const MANAGE_TRANSACTION_REFUND = 'manage.transaction.refund'; // 申请退款
    public const MANAGE_BASIC_SET_DISTRIBUTION = 'manage.basic_set.distribution'; // 配送管理
    public const MANAGE_SHIP_COMPANY_INDEX = 'manage.ship_company.index'; // 快递公司
    public const MANAGE_SHIP_COMPANY_UPDATE = 'manage.ship_company.update'; // 快递公司编辑
    public const WEBSITE_MANAGE = 'website.manage'; // 网站管理
    public const MANAGE_APP_DECORATION = 'manage.app_decoration.index'; // 移动端装修
    public const MANAGE_APP_DECORATION_UPDATE = 'manage.app_decoration.update'; // 移动端保存装修
    public const ADVERT_MANAGE = 'advert.manage'; // 广告管理
    public const MANAGE_APP_ADVERT_INDEX = 'manage.app_advert.index'; // app广告
    public const MANAGE_APP_ADVERT_UPDATE = 'manage.app_advert.update'; // app广告新增|编辑
    public const MANAGE_APP_ADVERT_DELETE = 'manage.app_advert.delete'; // app广告删除

    // 二级 商品部分
    public const GOODS_MANAGE = 'goods.manage'; // 商品管理
    public const MANAGE_GOODS_INDEX = 'manage.goods.index'; // 商品列表
    public const MANAGE_GOODS_UPDATE = 'manage.goods.update'; // 商品列表新增|编辑
    public const CATEGORY_MANAGE = 'category.manage'; // 分类管理
    public const MANAGE_CATEGORY_INDEX = 'manage.category.index'; // 商品分类
    public const MANAGE_CATEGORY_UPDATE = 'manage.category.update'; // 商品分类新增|编辑
    public const MANAGE_CATEGORY_DELETE = 'manage.category.delete'; // 商品分类删除
    public const GOODS_DATA_MANAGE = 'goods_data.manage'; // 商品数据
    public const MANAGE_GOODS_VIEWS = 'manage.goods_views.index'; // 商品浏览
    public const MANAGE_GOODS_COLLECT = 'manage.goods_collect.index'; // 商品收藏

    // 二级 用户部分
    public const USER_MANAGE = 'user.manage'; // 会员管理
    public const MANAGE_USER_INDEX = 'manage.user.index'; // 会员列表
    public const MANAGE_USER_UPDATE = 'manage.user.update'; // 会员列表新增|编辑
    public const AUTHORIZED_USER_MANAGE = 'authorized_user.manage'; // 授权用户
    public const MANAGE_WECHAT_USER_INDEX = 'manage.wechat_user.index'; // 微信服务号
    public const INTEGRAL_MANAGE = 'integral.manage'; // 积分管理
    public const MANAGE_USER_INTEGRAL_INDEX = 'manage.user_integral.index'; // 用户积分
    public const MANAGE_INTEGRAL_DETAIL_INDEX = 'manage.integral_detail.index'; // 积分明细

    // 二级 工具部分
    public const MANAGE_MATERIAL_CENTER = 'manage.material_center.index'; // 素材中心
    public const MANAGE_MATERIAL_CENTER_UPDATE = 'manage.material_center.update'; // 素材中心 新增|编辑
    public const MANAGE_MATERIAL_CENTER_DELETE = 'manage.material_center.delete'; // 素材中心 删除

    // 二级 订单部分
    public const ORDER_MANAGE = 'order.manage'; // 订单管理
    public const MANAGE_ORDER_INDEX = 'manage.order.index'; // 订单列表
    public const APPLY_REFUND_MANAGE = 'apply_refund.manage'; // 退款管理
    public const MANAGE_APPLY_REFUND_REASON_INDEX = 'manage.apply_refund_reason.index'; // 退款原因
    public const MANAGE_APPLY_REFUND_REASON_UPDATE = 'manage.apply_refund_reason.update'; // 退款原因新增|编辑
    public const MANAGE_APPLY_REFUND_REASON_DELETE = 'manage.apply_refund_reason.delete'; // 退款原因删除
    public const MANAGE_APPLY_REFUND_INDEX = 'manage.apply_refund.index'; // 退款申请
    public const MANAGE_APPLY_REFUND_UPDATE = 'manage.apply_refund.update'; // 退款申请编辑
    public const ORDER_DELIVERY_MANAGE = 'order_delivery.manage'; // 发货管理
    public const MANAGE_ORDER_DELIVERY_INDEX = 'manage.order_delivery.index'; // 发货列表
    public const MANAGE_ORDER_DELIVERY_UPDATE = 'manage.order_delivery.update'; // 发货记录编辑
    public const ORDER_EVALUATE_MANAGE = 'order_evaluate.manage'; // 订单评价管理
    public const MANAGE_ORDER_EVALUATE_INDEX = 'manage.order_evaluate.index'; // 订单评价
    public const MANAGE_ORDER_EVALUATE_UPDATE = 'manage.order_evaluate.update'; // 编辑订单评价

    // 二级 文章部分
    public const ARTICLE_MANAGE = 'article.manage'; // 文章管理
    public const MANAGE_ARTICLE_CATEGORY_INDEX = 'manage.article_category.index'; // 文章分类
    public const MANAGE_ARTICLE_CATEGORY_UPDATE = 'manage.article_category.update'; // 文章分类新增|编辑
    public const MANAGE_ARTICLE_CATEGORY_DELETE = 'manage.article_category.delete'; // 文章分类删除
    public const MANAGE_ARTICLE_INDEX = 'manage.article.index'; // 文章列表
    public const MANAGE_ARTICLE_UPDATE = 'manage.article.update'; // 文章列表新增|编辑
    public const MANAGE_ARTICLE_DELETE = 'manage.article.delete'; // 文章列表删除

    // 二级 营销部分
    public const DISCOUNT_MANAGE = 'discount.manage'; // 优惠管理
    public const MANAGE_BONUS_INDEX = 'manage.bonus.index'; // 红包
    public const MANAGE_USER_BONUS_INDEX = 'manage.user_bonus.index'; // 查看用户红包
    public const MANAGE_COUPON_INDEX = 'manage.coupon.index'; // 优惠券
    public const MANAGE_USER_COUPON_INDEX = 'manage.user_coupon.index'; // 查看用户优惠券

    // 二级 数据部分
    public const STATISTIC_MANAGE = 'statistic.manage'; // 数据统计
    public const MANAGE_ACCESS_STATISTIC_INDEX = 'manage.access_statistic.index'; // 访问统计

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

    public function childrens()
    {
        return $this->hasMany(Permission::class, 'parent_id', 'id');
    }
}
