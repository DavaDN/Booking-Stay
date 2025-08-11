<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::create([
            'room_type_id' => 1,
            'number' => '101',
            'status' => 'available'
        ]);

        Room::create([
            'room_type_id' => 1,
            'number' => '102',
            'status' => 'available'
        ]);

        Room::create([
            'room_type_id' => 2,
            'number' => '201',
            'status' => 'available'
        ]);

        Room::create([
            'room_type_id' => 3,
            'number' => '301',
            'status' => 'available'
        ]);
    }
}
