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
        $guard_name = (new Permission)->guardName();

        $this->addSettingsPermission($guard_name);
        $this->addGoodsPermission($guard_name);
        $this->addUserPermission($guard_name);
        $this->addOrderPermission($guard_name);
        $this->addArticlePermission($guard_name);
        $this->addToolPermission($guard_name);
        $this->addDataPermission($guard_name);
    }

    private function addSettingsPermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '设置', Permission::MANAGE_SETTINGS, 100, '', Permission::IS_LEFT_NAV);
    }

    private function addGoodsPermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '商品', Permission::MANAGE_GOODS, 99, '', Permission::IS_LEFT_NAV);
        $this->addPermission($guard_name, '商品管理', Permission::MANAGE_GOODS_SETTINGS, 100, '', Permission::IS_LEFT_NAV, Permission::MANAGE_GOODS);
        $this->addPermission($guard_name, '商品列表', Permission::MANAGE_GOODS_INDEX, 100, '', Permission::IS_LEFT_NAV, Permission::MANAGE_GOODS_SETTINGS);
        $this->addPermission($guard_name, '商品分类', Permission::MANAGE_GOODS_CATEGORY, 99, '', Permission::IS_LEFT_NAV, Permission::MANAGE_GOODS_SETTINGS);
        $this->addPermission($guard_name, '商品品牌', Permission::MANAGE_GOODS_BRAND, 98, '', Permission::IS_LEFT_NAV, Permission::MANAGE_GOODS_SETTINGS);
        $this->addPermission($guard_name, '商品标签', Permission::MANAGE_GOODS_LABELS, 97, '', Permission::IS_LEFT_NAV, Permission::MANAGE_GOODS_SETTINGS);
        $this->addPermission($guard_name, '商品保障', Permission::MANAGE_GOODS_GUARANTEE, 96, '', Permission::IS_LEFT_NAV, Permission::MANAGE_GOODS_SETTINGS);
        $this->addPermission($guard_name, '商品规格', Permission::MANAGE_GOODS_SKU_TEMPLATE, 95, '', Permission::IS_LEFT_NAV, Permission::MANAGE_GOODS_SETTINGS);
    }

    private function addUserPermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '用户', Permission::MANAGE_USER, 98, '', Permission::IS_LEFT_NAV);
    }

    private function addOrderPermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '订单', Permission::MANAGE_ORDER, 97, '', Permission::IS_LEFT_NAV);
    }

    private function addArticlePermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '文章', Permission::MANAGE_ARTICLE, 96, '', Permission::IS_LEFT_NAV);
    }

    private function addToolPermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '工具', Permission::MANAGE_TOOLS, 95, '', Permission::IS_LEFT_NAV);
    }

    private function addDataPermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '数据', Permission::MANAGE_DATA, 94, '', Permission::IS_LEFT_NAV);
    }

    private function addPermission(
        string $guard_name,
        string $display_name,
        string $name,
        int $sort = 0,
        string $icon = '',
        int $is_left_nav = Permission::NOT_IS_LEFT_NAV,
        ?string $parent_name = null,
    ): void {
        $permission = Permission::query()->firstOrNew([
            'name' => $name,
            'guard_name' => $guard_name,
        ]);

        if (! $permission->exists) {
            $permission->display_name = $display_name;
            $permission->parent_id = $parent_name ? Permission::query()->whereName($parent_name)->value('id') : 0;
            $permission->sort = $sort;
            $permission->icon = $icon;
            $permission->is_left_nav = $is_left_nav;
            $permission->save();
        }
    }
}
