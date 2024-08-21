<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ModuleSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            PermissionsSeeder::class,
            LoanDurationSeeder::class,
            ProductServiceSeeder::class,
            LoanPurposeSeeder::class,
        ]);
    }
}
