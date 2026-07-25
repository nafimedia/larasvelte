<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SiteSettingSeeder::class,
            UserSeeder::class,
            ModuleSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            PostSeeder::class,
            CmsMenuSeeder::class,
            FormSeeder::class,
        ]);
    }
}
