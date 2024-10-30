<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = array (
            'Users',
            'Roles',
            'Customer',
            'Loan Management',
            'Installments',
            'Complaints',
            'Recoveries',
            'Products',
        );

        foreach ($modules as $row) {
            Module::firstOrCreate([
                'name' => $row,
            ]);
        }
    }
}
