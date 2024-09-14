<?php

namespace Database\Seeders;

use App\Models\MaritalStatus;
use Illuminate\Database\Seeder;

class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Single', 'emoji' => 'smile'],
            ['name' => 'Married', 'emoji' => 'ring'],
            ['name' => 'Divorced', 'emoji' => 'broken_heart'],
            ['name' => 'Widowed', 'emoji' => 'cry'],
        ];

        foreach ($statuses as $status) {
            MaritalStatus::firstOrCreate([
                'name' => $status['name'],
                'emoji' => $status['emoji']
            ]);
        }
    }
}
