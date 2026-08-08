<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $settings = CompanySetting::getSettings();
        $categories = PropertyCategory::where('status', 'active')->get();
        $locations = Location::where('status', 'active')->get();

        $query = Property::with(['category', 'location', 'images'])
            ->where('status', 'published');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('type')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->type)->orWhere('id', $request->type);
            });
        }

        if ($request->filled('location')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('slug', $request->location)->orWhere('id', $request->location);
            });
        }

        if ($request->filled('price_range')) {
            switch ($request->price_range) {
                case 'under_250k':
                    $query->where('price', '<', 250000);
                    break;
                case '250k_500k':
                    $query->whereBetween('price', [250000, 500000]);
                    break;
                case 'above_500k':
                    $query->where('price', '>', 500000);
                    break;
            }
        }

        if ($request->filled('date_uploaded')) {
            if ($request->date_uploaded === 'last_30_days') {
                $query->where('created_at', '>=', now()->subDays(30));
            } elseif ($request->date_uploaded === 'last_7_days') {
                $query->where('created_at', '>=', now()->subDays(7));
            }
        }

        $properties = $query->latest()->paginate(9)->withQueryString();

        return view('public.properties.index', compact('properties', 'categories', 'locations', 'settings'));
    }

    public function show($slug)
    {
        $settings = CompanySetting::getSettings();
        $property = Property::with(['category', 'location', 'images'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Increment view counter safely
        $property->increment('views_count');

        $similarProperties = Property::with(['category', 'location', 'images'])
            ->where('status', 'published')
            ->where('id', '!=', $property->id)
            ->where('category_id', $property->category_id)
            ->take(3)
            ->get();

        return view('public.properties.show', compact('property', 'similarProperties', 'settings'));
    }
}
