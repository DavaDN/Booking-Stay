<?php

// database/seeders/FloorSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Floor;

class FloorSeeder extends Seeder
{
    public function run(): void
    {
        Floor::insert([
            ['name' => 'Lantai 1'],
            ['name' => 'Lantai 2'],
            ['name' => 'Lantai 3'],
        ]);
    }
}
