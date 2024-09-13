<?php

namespace Database\Seeders;

use App\Models\Relationship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $relationships = [
            ['name' => 'Father'],
            ['name' => 'Mother'],
            ['name' => 'Son'],
            ['name' => 'Daughter'],
            ['name' => 'Brother'],
            ['name' => 'Sister'],
            ['name' => 'Husband'],
            ['name' => 'Wife'],
            ['name' => 'Grandfather'],
            ['name' => 'Grandmother'],
            ['name' => 'Uncle'],
            ['name' => 'Aunt'],
            ['name' => 'Cousin'],
            ['name' => 'Nephew'],
            ['name' => 'Niece'],
            ['name' => 'Stepfather'],
            ['name' => 'Stepmother'],
            ['name' => 'Stepbrother'],
            ['name' => 'Stepsister'],
            ['name' => 'Father-in-law'],
            ['name' => 'Mother-in-law'],
            ['name' => 'Brother-in-law'],
            ['name' => 'Sister-in-law'],
        ];

        foreach ($relationships as $relationship) {
            Relationship::firstOrCreate($relationship);
        }
    }
}
