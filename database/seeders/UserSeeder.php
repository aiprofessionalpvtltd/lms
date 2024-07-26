<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@admin.com',
            'password' => '123456',
            'email_verified_at' => currentDateTimeInsert(),

        ]);

        $user->assignRole(Role::where('name', 'Super Admin')->first());


        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => '123456',
            'email_verified_at' => currentDateTimeInsert(),
        ]);

        $user->assignRole(Role::where('name', 'Admin')->first());
    }
}
