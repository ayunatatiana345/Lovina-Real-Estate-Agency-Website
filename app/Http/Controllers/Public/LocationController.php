<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Property;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $settings = CompanySetting::getSettings();
        
        $query = Location::where('status', 'active')->withCount(['properties' => function ($q) {
            $q->where('status', 'published');
        }]);

        if ($request->filled('keyword')) {
            $query->where('name', 'like', "%{$request->keyword}%");
        }

        $locations = $query->get();
        $totalLocations = Location::where('status', 'active')->count();
        $totalProperties = Property::where('status', 'published')->count();

        return view('public.locations.index', compact('locations', 'totalLocations', 'totalProperties', 'settings'));
    }
}
