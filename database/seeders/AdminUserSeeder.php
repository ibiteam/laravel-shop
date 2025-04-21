<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin_user = AdminUser::query()->firstOrNew(['user_name' => 'admin']);

        if (! $admin_user->exists) {
            $admin_user->user_name = 'admin';
            $admin_user->phone = '13311112222';
            $admin_user->password = 'Aa123456';
            $admin_user->save();
        }
    }
}
