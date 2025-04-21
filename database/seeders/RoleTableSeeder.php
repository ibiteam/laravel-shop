<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::query()->firstOrNew(['name' => 'root']);

        if (! $role->exists) {
            $role->guard_name = $role->guardName();
            $role->display_name = '超级管理员';
            $role->save();

            $admin_user = AdminUser::query()->whereUserName('admin')->first();

            if ($admin_user) {
                $admin_user->assignRole($role);
            }
        }
        $role->givePermissionTo(Permission::query()->get());
    }
}
