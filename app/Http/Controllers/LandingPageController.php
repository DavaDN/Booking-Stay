<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    // Web landing page
    public function index()
    {
        $roomTypes = RoomType::with('rooms')->get();
        return view('landing.index', compact('roomTypes'));
    }

    // API landing page (untuk mobile)
    public function apiRoomTypes()
    {
        $roomTypes = RoomType::with('rooms')->get();
        return response()->json($roomTypes);
    }
}
