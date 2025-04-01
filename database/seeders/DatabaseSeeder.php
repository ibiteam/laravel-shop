<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(ShopConfigTableSeeder::class);
        $this->call(AppWebsiteDecorationTableSeeder::class);
        $this->call(RegionSeeder::class);
        $this->call(SensitiveWordSeeder::class);
    }
}
