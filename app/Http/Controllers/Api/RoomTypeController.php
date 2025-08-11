<?php


namespace App\Http\Controllers\Api;

use App\Models\RoomType;
use App\Http\Controllers\Controller;

class RoomTypeController extends Controller
{
    public function index()
    {
        return RoomType::with(['facilities', 'room'])->get();
    }

    public function show($id)
    {
        return RoomType::with(['facilities', 'room'])->findOrFail($id);
    }
}
