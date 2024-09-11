<?php

namespace Database\Seeders;

use App\Models\EmploymentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmploymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Employed',
            'Self-Employed',
            'Unemployed',
            'Retired',
            'Student',
        ];

        foreach ($statuses as $status) {
            EmploymentStatus::firstOrCreate(['status' => $status]);
        }
    }
}
