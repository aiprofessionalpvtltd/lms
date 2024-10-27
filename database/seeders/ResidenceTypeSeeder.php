<?php

namespace Database\Seeders;

use App\Models\ResidenceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResidenceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Own house'],
            ['name' => 'Rented'],
            ['name' => 'Temporary'],

        ];

        foreach ($types as $type) {
            ResidenceType::firstOrCreate($type);
        }
    }
}
