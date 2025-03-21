<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Permission;
use App\Models\Role;
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
        $this->addTemplatePermission($guard_name);
        // 初始化管理员角色
        $this->initRole($guard_name);
    }

    private function initRole($guard_name)
    {
        $role = Role::query()->firstOrNew(['name' => 'root']);

        if (! $role->exists) {
            $role->guard_name = $guard_name;
            $role->display_name = '超级管理员';
            $role->save();

            $admin_user = AdminUser::whereUserName('admin')->first();

            if ($admin_user) {
                $admin_user->assignRole($role);
            }
        }

        $this->give_permission($role);
    }

    private function give_permission(Role $role)
    {
        $permissions = Permission::query()->get();
        $role->givePermissionTo($permissions);
    }

    private function addSettingsPermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '设置', Permission::MANAGE_SETTINGS, 100, '', Permission::IS_LEFT_NAV);
    }

    private function addStorePermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '店铺', Permission::MANAGE_STORE, 99, '', Permission::IS_LEFT_NAV);

        // 入驻管理
        $this->addPermission($guard_name, '入驻管理',Permission::SELLER_ENTER_MANAGE,9,'',Permission::IS_LEFT_NAV,Permission::MANAGE_STORE);
        $this->addPermission($guard_name, '商家注册项设置',Permission::SELLER_ENTER_CONFIG_INDEX,100,'',Permission::IS_LEFT_NAV,Permission::SELLER_ENTER_MANAGE);
        $this->addPermission($guard_name, '商家注册项设置添加|编辑',Permission::SELLER_ENTER_CONFIG_UPDATE,99,'',Permission::NOT_IS_LEFT_NAV,Permission::SELLER_ENTER_MANAGE);
        $this->addPermission($guard_name, '入驻商家列表',Permission::SELLER_ENTER_INDEX,90,'',Permission::IS_LEFT_NAV,Permission::SELLER_ENTER_MANAGE);
        $this->addPermission($guard_name, '入驻商家添加|编辑',Permission::SELLER_ENTER_UPDATE,89,'',Permission::NOT_IS_LEFT_NAV,Permission::SELLER_ENTER_MANAGE);
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
    private function addTemplatePermission(string $guard_name): void
    {
        $this->addPermission($guard_name, '模板', Permission::MANAGE_TEMPLATE, 94, '', Permission::IS_LEFT_NAV);
        $this->addTemplate($guard_name);
    }

    // 模板
    private function addTemplate($guard_name)
    {
        // 模板-网站管理
        $this->addPermission($guard_name, '网站管理', Permission::WEB_DECORATION_MANAGE, 10, '', Permission::IS_LEFT_NAV, Permission::MANAGE_TEMPLATE);
        $this->addPermission($guard_name, '移动端装修', Permission::APP_WEB_DECORATION_INDEX, 90, '', Permission::IS_LEFT_NAV, Permission::WEB_DECORATION_MANAGE);
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
