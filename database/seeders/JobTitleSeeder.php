<?php

namespace Database\Seeders;

use App\Models\JobTitle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            ['name' => 'Trainee/Junior Staff'],
            ['name' => 'Senior/Managerial'],
            ['name' => 'Executive/Director'],
            ['name' => 'Business Owner/Entrepreneur'],
            ['name' => 'Doctor'],
            ['name' => 'Engineer'],
            ['name' => 'Teacher/Principal'],
            ['name' => 'Other'],

        ];

        foreach ($titles as $title) {
            JobTitle::firstOrCreate($title);
        }
    }
}
