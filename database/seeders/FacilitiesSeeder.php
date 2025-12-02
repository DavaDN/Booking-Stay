<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facilities;

class FacilitiesSeeder extends Seeder
{
    public function run(): void
    {
        Facilities::create([
            'name' => 'Free Wi-Fi',
            'image' => 'wifi.jpg'
        ]);
        Facilities::create([
            'name' => 'AC',
            'image' => 'ac.jpg'
        ]);

        Facilities::create([
            'name' => 'Breakfast Included',
            'image' => 'breakfast.jpg'
        ]);
        Facilities::create([
            'name' => 'TV Cable',
            'image' => 'tv.jpg'
        ]);

        Facilities::create([
            'name' => 'Private Pool',
            'image' => 'pool.jpg'
        ]);
        Facilities::create([
            'name' => 'Mini Bar',
            'image' => 'minibar.jpg'
        ]);
    }
}
