<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        RoomType::create([
            'name' => 'Deluxe Room',
            'price' => 500000,
            'capacity' => 2,
            'description' => 'Kamar luas dengan fasilitas lengkap untuk dua orang.'
            
        ]);

        RoomType::create([
            'name' => 'Superior Room',
            'price' => 350000,
            'capacity' => 2,
            'description' => 'Kamar nyaman dengan pemandangan indah.'
        ]);

        RoomType::create([
            'name' => 'Suite Room',
            'price' => 1000000,
            'capacity' => 4,
            'description' => 'Kamar mewah dengan ruang tamu dan fasilitas VIP.'
        ]);
    }
}
