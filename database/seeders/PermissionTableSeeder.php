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
        $this->addStorePermission($guard_name);
        $this->addUserPermission($guard_name);
        $this->addArticlePermission($guard_name);
        $this->addToolPermission($guard_name);
        $this->addDataPermission($guard_name);
        $this->addProductPermission($guard_name);
    }

    private function addSettingsPermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '设置', Permission::MANAGE_SETTINGS, 100, '', Permission::IS_LEFT_NAV);
    }

    private function addStorePermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '店铺', Permission::MANAGE_STORE, 99, '', Permission::IS_LEFT_NAV);
    }

    private function addUserPermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '用户', Permission::MANAGE_USER, 98, '', Permission::IS_LEFT_NAV);
    }

    private function addArticlePermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '文章', Permission::MANAGE_ARTICLE, 97, '', Permission::IS_LEFT_NAV);
    }

    private function addProductPermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '产品', Permission::MANAGE_PRODUCT, 94, '', Permission::IS_LEFT_NAV);
    }

    private function addToolPermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '工具', Permission::MANAGE_TOOLS, 96, '', Permission::IS_LEFT_NAV);
    }

    private function addDataPermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '数据', Permission::MANAGE_DATA, 95, '', Permission::IS_LEFT_NAV);
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
