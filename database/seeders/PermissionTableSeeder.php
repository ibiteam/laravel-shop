<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

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

        $this->addPermission('基础设置', Permission::BASIC_SET_MANAGE, 99, Permission::IS_LEFT_NAV, 'Menu', Permission::MODULE_SET);

        $this->addPermission('商店设置', Permission::MANAGE_SHOP_CONFIG_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('商店设置编辑', Permission::MANAGE_SHOP_CONFIG_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);

        $this->addPermission('访问地址分类', Permission::MANAGE_ROUTER_CATEGORY_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('访问地址分类新增|编辑', Permission::MANAGE_ROUTER_CATEGORY_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('访问地址分类删除', Permission::MANAGE_ROUTER_CATEGORY_DELETE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);

        $this->addPermission('访问地址', Permission::MANAGE_ROUTER_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('访问地址新增|编辑', Permission::MANAGE_ROUTER_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);
        $this->addPermission('访问地址删除', Permission::MANAGE_ROUTER_DELETE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_SET_MANAGE);

        $this->addPermission('支付管理', Permission::BASIC_PAYMENT_MANAGE, 98, Permission::IS_LEFT_NAV, 'Menu', Permission::MODULE_SET);

        $this->addPermission('支付方式', Permission::MANAGE_PAYMENT_METHOD_INDEX, 99, Permission::IS_LEFT_NAV, '', Permission::BASIC_PAYMENT_MANAGE);
        $this->addPermission('支付方式编辑', Permission::MANAGE_PAYMENT_METHOD_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::BASIC_PAYMENT_MANAGE);
    }

    private function addGoodsPermission(): void
    {
        $this->addPermission('商品', Permission::MODULE_GOODS, 99, Permission::IS_LEFT_NAV, 'Goods');

        $this->addPermission('商品管理', Permission::GOODS_MANAGE, 0, Permission::IS_LEFT_NAV, 'Menu', Permission::MODULE_GOODS);

        $this->addPermission('商品列表', Permission::MANAGE_GOODS_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::GOODS_MANAGE);
        $this->addPermission('商品列表新增|编辑', Permission::MANAGE_GOODS_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::GOODS_MANAGE);

        $this->addPermission('分类管理', Permission::CATEGORY_MANAGE, 0, Permission::IS_LEFT_NAV, 'Menu', Permission::MODULE_GOODS);

        $this->addPermission('商品分类', Permission::MANAGE_CATEGORY_INDEX, 0, Permission::IS_LEFT_NAV, '', Permission::CATEGORY_MANAGE);
        $this->addPermission('商品分类新增|编辑', Permission::MANAGE_CATEGORY_UPDATE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::CATEGORY_MANAGE);
        $this->addPermission('商品分类删除', Permission::MANAGE_CATEGORY_DELETE, 0, Permission::NOT_IS_LEFT_NAV, '', Permission::CATEGORY_MANAGE);
    }

    private function addUserPermission(): void
    {
        $this->addPermission('用户', Permission::MODULE_USER, 98, Permission::IS_LEFT_NAV, 'User');
    }

    private function addOrderPermission(): void
    {
        $this->addPermission('订单', Permission::MODULE_ORDER, 97, Permission::IS_LEFT_NAV, 'ShoppingBag');
    }

    private function addArticlePermission(): void
    {
        $this->addPermission('文章', Permission::MODULE_ARTICLE, 96, Permission::IS_LEFT_NAV, 'Notebook');
    }

    private function addToolPermission(): void
    {
        $this->addPermission('工具', Permission::MODULE_TOOLS, 95, Permission::IS_LEFT_NAV, 'Tools');
    }

    private function addDataPermission(): void
    {
        $this->addPermission('数据', Permission::MODULE_DATA, 94, Permission::IS_LEFT_NAV, 'DataAnalysis');
    }
}
