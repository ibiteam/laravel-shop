<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->addSetPermission();
        $this->addGoodsPermission();
        $this->addUserPermission();
        $this->addOrderPermission();
        $this->addArticlePermission();
        $this->addToolPermission();
        $this->addDataPermission();
        $this->addMarketingPermission();
    }

    private function addPermission(string $display_name, string $name, int $sort = 0, int $is_left_nav = Permission::NOT_IS_LEFT_NAV, string $icon = '', ?string $parent_name = null): void
    {
        $permission = Permission::query()->firstOrNew([
            'name' => $name,
            'guard_name' => (new Permission)->guardName(),
        ]);

        if (! $permission->exists) {
            $permission->display_name = $display_name;
            $permission->parent_id = $parent_name ? Permission::query()->whereName($parent_name)->value('id') : 0;
            $permission->sort = $sort;
            $permission->is_left_nav = $is_left_nav;
            $permission->icon = $icon;
            $permission->save();
        }
    }

    private function addSetPermission(): void
    {
        $this->addPermission('设置', Permission::MODULE_SET, 100, Permission::IS_LEFT_NAV, 'Setting');

        $this->addPermission('基础设置', Permission::BASIC_SET_MANAGE, 99, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_SET);
        $this->addPermission('商店设置', Permission::MANAGE_SHOP_CONFIG_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('商店设置编辑', Permission::MANAGE_SHOP_CONFIG_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('访问地址分类', Permission::MANAGE_ROUTER_CATEGORY_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('访问地址分类新增|编辑', Permission::MANAGE_ROUTER_CATEGORY_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('访问地址分类删除', Permission::MANAGE_ROUTER_CATEGORY_DELETE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('访问地址', Permission::MANAGE_ROUTER_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('访问地址新增|编辑', Permission::MANAGE_ROUTER_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('访问地址删除', Permission::MANAGE_ROUTER_DELETE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        // 外部服务
        $this->addPermission('外部服务', Permission::MANAGE_APP_SERVICE_CONFIG_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('访问地址新增|编辑', Permission::MANAGE_APP_SERVICE_CONFIG_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('外部服务日志', Permission::MANAGE_APP_SERVICE_LOG_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);

        $this->addPermission('权限管理', Permission::PERMISSION_MANAGE, 98, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_SET);
        $this->addPermission('管理员列表', Permission::MANAGE_ADMIN_USER_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::PERMISSION_MANAGE);
        $this->addPermission('管理员新增|编辑', Permission::MANAGE_ADMIN_USER_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::PERMISSION_MANAGE);
        $this->addPermission('权限菜单', Permission::MANAGE_PERMISSION_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::PERMISSION_MANAGE);
        $this->addPermission('权限菜单编辑', Permission::MANAGE_PERMISSION_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::PERMISSION_MANAGE);
        $this->addPermission('角色管理', Permission::MANAGE_ROLE_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::PERMISSION_MANAGE);
        $this->addPermission('角色新增|编辑', Permission::MANAGE_ROLE_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::PERMISSION_MANAGE);
        $this->addPermission('角色删除', Permission::MANAGE_ROLE_DELETE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::PERMISSION_MANAGE);
        $this->addPermission('管理员日志', Permission::MANAGE_ADMIN_OPERATION_LOG_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::PERMISSION_MANAGE);

        $this->addPermission('配送管理', Permission::MANAGE_BASIC_SET_DISTRIBUTION, 0, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_SET);
        $this->addPermission('快递公司', Permission::MANAGE_SHIP_COMPANY_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::MANAGE_BASIC_SET_DISTRIBUTION);
        $this->addPermission('快递公司新增|编辑', Permission::MANAGE_SHIP_COMPANY_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::MANAGE_BASIC_SET_DISTRIBUTION);

        $this->addPermission('支付管理', Permission::BASIC_PAYMENT_MANAGE, 98, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_SET);
        $this->addPermission('支付方式', Permission::MANAGE_PAYMENT_INDEX, 99, Permission::IS_LEFT_NAV, '', Permission::BASIC_PAYMENT_MANAGE);
        $this->addPermission('支付方式编辑', Permission::MANAGE_PAYMENT_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_PAYMENT_MANAGE);
        $this->addPermission('交易记录', Permission::MANAGE_TRANSACTION_INDEX, 98, Permission::IS_LEFT_NAV, '', Permission::BASIC_PAYMENT_MANAGE);
        $this->addPermission('交易记录-申请退款', Permission::MANAGE_TRANSACTION_REFUND, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_PAYMENT_MANAGE);

        $this->addPermission('网站管理', Permission::WEBSITE_MANAGE, 1, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_SET);
        $this->addPermission('移动端装修', Permission::MANAGE_APP_DECORATION, 1, Permission::IS_LEFT_NAV, '', Permission::WEBSITE_MANAGE);
        $this->addPermission('移动端保存装修', Permission::MANAGE_APP_DECORATION_UPDATE, 1, Permission::NOT_IS_LEFT_NAV, '', Permission::WEBSITE_MANAGE);

        $this->addPermission('广告管理', Permission::ADVERT_MANAGE, 97, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_SET);
        $this->addPermission('app广告', Permission::MANAGE_APP_ADVERT_INDEX, 1, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::ADVERT_MANAGE);
        $this->addPermission('app广告新增|编辑', Permission::MANAGE_APP_ADVERT_UPDATE, 1, Permission::NOT_IS_LEFT_NAV, 'icon-caidan', Permission::ADVERT_MANAGE);
        $this->addPermission('app广告删除', Permission::MANAGE_APP_ADVERT_DELETE, 1, Permission::NOT_IS_LEFT_NAV, 'icon-caidan', Permission::ADVERT_MANAGE);
    }

    private function addGoodsPermission(): void
    {
        $this->addPermission('商品', Permission::MODULE_GOODS, 99, Permission::IS_LEFT_NAV, 'Goods');

        $this->addPermission('商品管理', Permission::GOODS_MANAGE, 0, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_GOODS);
        $this->addPermission('商品列表', Permission::MANAGE_GOODS_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::GOODS_MANAGE);
        $this->addPermission('商品列表新增|编辑', Permission::MANAGE_GOODS_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::GOODS_MANAGE);

        $this->addPermission('分类管理', Permission::CATEGORY_MANAGE, 0, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_GOODS);
        $this->addPermission('商品分类', Permission::MANAGE_CATEGORY_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::CATEGORY_MANAGE);
        $this->addPermission('商品分类新增|编辑', Permission::MANAGE_CATEGORY_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::CATEGORY_MANAGE);
        $this->addPermission('商品分类删除', Permission::MANAGE_CATEGORY_DELETE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::CATEGORY_MANAGE);

        $this->addPermission('商品数据', Permission::GOODS_DATA_MANAGE, 0, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_GOODS);
        $this->addPermission('商品浏览', Permission::MANAGE_GOODS_VIEWS, 0, Permission::IS_LEFT_NAV, '', Permission::GOODS_DATA_MANAGE);
        $this->addPermission('商品收藏', Permission::MANAGE_GOODS_COLLECT, 0, Permission::IS_LEFT_NAV, '', Permission::GOODS_DATA_MANAGE);
    }

    private function addUserPermission(): void
    {
        $this->addPermission('用户', Permission::MODULE_USER, 98, Permission::IS_LEFT_NAV, 'User');

        $this->addPermission('会员管理', Permission::USER_MANAGE, 0, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_USER);
        $this->addPermission('会员列表', Permission::MANAGE_USER_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::USER_MANAGE);
        $this->addPermission('会员列表新增|编辑', Permission::MANAGE_USER_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::USER_MANAGE);

        $this->addPermission('授权用户', Permission::AUTHORIZED_USER_MANAGE, 0, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_USER);
        $this->addPermission('微信服务号', Permission::MANAGE_WECHAT_USER_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::AUTHORIZED_USER_MANAGE);

        $this->addPermission('积分管理', Permission::INTEGRAL_MANAGE, 0, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_USER);
        $this->addPermission('用户积分', Permission::MANAGE_USER_INTEGRAL_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::INTEGRAL_MANAGE);
        $this->addPermission('积分明细', Permission::MANAGE_INTEGRAL_DETAIL_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::INTEGRAL_MANAGE);
    }

    private function addOrderPermission(): void
    {
        $this->addPermission('订单', Permission::MODULE_ORDER, 97, Permission::IS_LEFT_NAV, 'ShoppingBag');

        $this->addPermission('订单管理', Permission::ORDER_MANAGE, 0, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_ORDER);
        $this->addPermission('订单列表', Permission::MANAGE_ORDER_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::ORDER_MANAGE);

        $this->addPermission('退款管理', Permission::APPLY_REFUND_MANAGE, 0, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_ORDER);
        $this->addPermission('退款原因', Permission::MANAGE_APPLY_REFUND_REASON_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::APPLY_REFUND_MANAGE);
        $this->addPermission('退款原因新增|编辑', Permission::MANAGE_APPLY_REFUND_REASON_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::APPLY_REFUND_MANAGE);
        $this->addPermission('退款原因删除', Permission::MANAGE_APPLY_REFUND_REASON_DELETE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::APPLY_REFUND_MANAGE);
        $this->addPermission('退款申请', Permission::MANAGE_APPLY_REFUND_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::APPLY_REFUND_MANAGE);
        $this->addPermission('退款申请编辑', Permission::MANAGE_APPLY_REFUND_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::APPLY_REFUND_MANAGE);

        $this->addPermission('发货管理', Permission::ORDER_DELIVERY_MANAGE, 0, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_ORDER);
        $this->addPermission('发货列表', Permission::MANAGE_ORDER_DELIVERY_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::ORDER_DELIVERY_MANAGE);
        $this->addPermission('发货导入|删除', Permission::MANAGE_ORDER_DELIVERY_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::ORDER_DELIVERY_MANAGE);

        $this->addPermission('评价管理', Permission::ORDER_EVALUATE_MANAGE, 0, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_ORDER);
        $this->addPermission('评价列表', Permission::MANAGE_ORDER_EVALUATE_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::ORDER_EVALUATE_MANAGE);
        $this->addPermission('评价编辑|审核', Permission::MANAGE_ORDER_EVALUATE_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::ORDER_EVALUATE_MANAGE);
    }

    private function addArticlePermission(): void
    {
        $this->addPermission('文章', Permission::MODULE_ARTICLE, 96, Permission::IS_LEFT_NAV, 'Notebook');

        $this->addPermission('文章管理', Permission::ARTICLE_MANAGE, 0, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_ARTICLE);
        $this->addPermission('文章分类', Permission::MANAGE_ARTICLE_CATEGORY_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::ARTICLE_MANAGE);
        $this->addPermission('文章分类新增|编辑', Permission::MANAGE_ARTICLE_CATEGORY_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::ARTICLE_MANAGE);
        $this->addPermission('文章分类删除', Permission::MANAGE_ARTICLE_CATEGORY_DELETE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::ARTICLE_MANAGE);
        $this->addPermission('文章列表', Permission::MANAGE_ARTICLE_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::ARTICLE_MANAGE);
        $this->addPermission('文章列表新增|编辑', Permission::MANAGE_ARTICLE_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::ARTICLE_MANAGE);
        $this->addPermission('文章列表删除', Permission::MANAGE_ARTICLE_DELETE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::ARTICLE_MANAGE);
    }

    private function addToolPermission(): void
    {
        $this->addPermission('工具', Permission::MODULE_TOOLS, 95, Permission::IS_LEFT_NAV, 'Tools');

        $this->addPermission('素材中心', Permission::MANAGE_MATERIAL_CENTER, 2, Permission::IS_LEFT_NAV, '', Permission::MODULE_TOOLS);
        $this->addPermission('素材中心新增|编辑', Permission::MANAGE_MATERIAL_CENTER_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::MODULE_TOOLS);
        $this->addPermission('素材中心删除', Permission::MANAGE_MATERIAL_CENTER_DELETE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::MODULE_TOOLS);

    }

    private function addDataPermission(): void
    {
        $this->addPermission('数据', Permission::MODULE_DATA, 94, Permission::IS_LEFT_NAV, 'DataAnalysis');
    }

    private function addMarketingPermission(): void
    {
        $this->addPermission('营销', Permission::MODULE_MARKETING, 93, Permission::IS_LEFT_NAV, 'DataAnalysis');

        $this->addPermission('优惠管理', Permission::DISCOUNT_MANAGE, 0, Permission::IS_LEFT_NAV, 'icon-caidan', Permission::MODULE_MARKETING);
        $this->addPermission('红包', Permission::MANAGE_BONUS_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::DISCOUNT_MANAGE);
        $this->addPermission('查看用户红包', Permission::MANAGE_USER_BONUS_INDEX, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::DISCOUNT_MANAGE);
        $this->addPermission('优惠券', Permission::MANAGE_COUPON_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::DISCOUNT_MANAGE);
        $this->addPermission('查看用户优惠券', Permission::MANAGE_USER_COUPON_INDEX, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::DISCOUNT_MANAGE);
    }
}
