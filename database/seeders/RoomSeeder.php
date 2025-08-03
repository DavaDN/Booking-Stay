<?php

// database/seeders/RoomSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::insert([
            ['number' => '101', 'floor_id' => 1, 'room_type_id' => 1, 'status' => 'available'],
            ['number' => '102', 'floor_id' => 1, 'room_type_id' => 2, 'status' => 'available'],
            ['number' => '201', 'floor_id' => 2, 'room_type_id' => 2, 'status' => 'maintenance'],
            ['number' => '202', 'floor_id' => 2, 'room_type_id' => 3, 'status' => 'booked'],
            ['number' => '301', 'floor_id' => 3, 'room_type_id' => 3, 'status' => 'available'],
        ]);
    }
}
