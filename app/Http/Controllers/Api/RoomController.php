<?php


namespace App\Http\Controllers\Api;

use App\Models\Room;
use App\Http\Controllers\Controller;

class RoomController extends Controller
{
    public function index()
    {
        return Room::with(['floor', 'roomType'])->where('status', 'available')->get();
    }

    public function show($id)
    {
        return Room::with(['floor', 'roomType'])->findOrFail($id);
    }
}
