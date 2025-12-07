<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Http\Request;


class LandingPageController extends Controller
{
    // Web landing page
    public function index() 
    {
        $hotels = Hotel::with('roomTypes.facilities')->get();
        $roomTypes = RoomType::with('facilities')->get(); 

        return view('landing.index', compact('hotels', 'roomTypes'));
    }

}
