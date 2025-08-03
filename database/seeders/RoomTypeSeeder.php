<?php

// database/seeders/RoomTypeSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        RoomType::insert([
            ['name' => 'Standard', 'price' => 300000, 'description' => 'Kamar dengan fasilitas standar dan tempat tidur tunggal.'],
            ['name' => 'Deluxe', 'price' => 500000, 'description' => 'Kamar lebih luas dengan tempat tidur queen dan fasilitas lengkap.'],
            ['name' => 'Suite', 'price' => 750000, 'description' => 'Kamar mewah dengan ruang tamu dan fasilitas eksklusif.'],
        ]);
    }
}
