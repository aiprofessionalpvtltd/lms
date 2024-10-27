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
            ['name' => 'Senior/Managerial'],
            ['name' => 'Mid-level'],
            ['name' => 'Entry-level/Fresh graduate'],

        ];

        foreach ($titles as $title) {
            JobTitle::firstOrCreate($title);
        }
    }
}
