<?php

namespace Database\Seeders;

use App\Models\MaritalStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['Single', 'Married', 'Divorced', 'Widowed'];

        foreach ($statuses as $status) {
            MaritalStatus::firstOrCreate(['name' => $status]);
        }
    }
}
