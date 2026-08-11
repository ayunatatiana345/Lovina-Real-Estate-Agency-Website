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
        $categories = \App\Models\PropertyCategory::where('status', 'active')->get();
        
        $query = Location::where('status', 'active')->withCount(['properties' => function ($q) {
            $q->where('status', 'published');
        }]);

        if ($request->filled('keyword')) {
            $query->where('name', 'like', "%{$request->keyword}%");
        }

        if ($request->filled('type')) {
            $query->whereHas('properties', function ($q) use ($request) {
                $q->where('status', 'published')
                  ->whereHas('category', function ($qc) use ($request) {
                      $qc->where('slug', $request->type);
                  });
            });
        }

        if ($request->filled('price_range')) {
            $query->whereHas('properties', function ($q) use ($request) {
                $q->where('status', 'published');
                if ($request->price_range == 'under_2b') {
                    $q->where('price', '<', 2000000000);
                } elseif ($request->price_range == '2b_to_5b') {
                    $q->where('price', '>=', 2000000000)->where('price', '<=', 5000000000);
                } elseif ($request->price_range == 'above_5b') {
                    $q->where('price', '>', 5000000000);
                }
            });
        }

        $locations = $query->orderByDesc('is_popular')
                           ->orderByDesc('properties_count')
                           ->orderBy('name')
                           ->get();
        $totalLocations = Location::where('status', 'active')->count();
        $totalProperties = Property::where('status', 'published')->count();

        return view('public.locations.index', compact('locations', 'totalLocations', 'totalProperties', 'settings', 'categories'));
    }
}
