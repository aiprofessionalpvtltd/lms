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
            'User',
            'Role',
            'Loan Management',

        );

        foreach ($modules as $row) {
            Module::firstOrCreate([
                'name' => $row,
            ]);
        }
    }
}
