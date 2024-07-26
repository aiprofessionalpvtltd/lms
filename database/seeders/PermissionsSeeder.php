<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = Module::all();

        $permissions = array(
            'View',
            'Create',
            'Edit',
            'Delete',
        );

        foreach ($modules as $module) {
            foreach ($permissions as $permission) {
                $permissionName = $permission . ' ' . $module->name;
                Permission::create(['short_name' => $permissionName, 'name' => str_replace(' ', '-', strtolower($permissionName)) , 'module' => $module->id]);
            }
        }


    }
}
