<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FacilityHotel;

class FacilityHotelSeeder extends Seeder
{
    public function run(): void
    {
        FacilityHotel::create([
            'name' => 'Free Wi-Fi',
            'description' => 'Internet berkecepatan tinggi gratis di seluruh area hotel',
            'image' => 'wifi.jpg'
        ]);

        FacilityHotel::create([
            'name' => 'Swimming Pool',
            'description' => 'Kolam renang outdoor dengan pemandangan kota',
            'image' => 'pool.jpg'
        ]);

        FacilityHotel::create([
            'name' =>  'Gym',
            'description' => 'Fasilitas gym dengan peralatan modern',
            'image' => 'gym.jpg'
        ]);

        FacilityHotel::create([
            'name' => 'Spa',
            'description' => 'Layanan spa profesional untuk relaksasi',
            'image' => 'spa.jpg'
        ]);
    }
}
