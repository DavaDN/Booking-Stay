<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facilities;

class FacilitiesSeeder extends Seeder
{
    public function run(): void
    {
        // Fasilitas untuk Deluxe Room (room_type_id = 1)
        Facilities::create([
            'room_type_id' => 1,
            'name' => 'Free Wi-Fi',
            'image' => 'wifi.jpg'
        ]);
        Facilities::create([
            'room_type_id' => 1,
            'name' => 'AC',
            'image' => 'ac.jpg'
        ]);

        // Fasilitas untuk Superior Room (room_type_id = 2)
        Facilities::create([
            'room_type_id' => 2,
            'name' => 'Breakfast Included',
            'image' => 'breakfast.jpg'
        ]);
        Facilities::create([
            'room_type_id' => 2,
            'name' => 'TV Cable',
            'image' => 'tv.jpg'
        ]);

        // Fasilitas untuk Suite Room (room_type_id = 3)
        Facilities::create([
            'room_type_id' => 3,
            'name' => 'Private Pool',
            'image' => 'pool.jpg'
        ]);
        Facilities::create([
            'room_type_id' => 3,
            'name' => 'Mini Bar',
            'image' => 'minibar.jpg'
        ]);
    }
}
