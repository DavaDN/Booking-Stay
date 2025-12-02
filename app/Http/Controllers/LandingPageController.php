<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    // Web landing page
    public function index()
    {
        $hotels = Hotel::with('rooms')->get();
        return response()->json([
            'success' => true,
            'message' => 'Landing Page Data',
            'data' => $hotels
        ]);

        return view('landing.index', compact('hotels'));
    }

}
