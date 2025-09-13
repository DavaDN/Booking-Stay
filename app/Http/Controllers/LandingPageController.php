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
        return response()->json([
            'success' => true,
            'message' => 'Landing Page Data',
            'data' => $roomTypes
        ]);

        return view('landing.index', compact('roomTypes'));
    }

}
