<?php

// Tatiana handles locations here.

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Property;
use App\Models\CompanySetting;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request, CurrencyService $currencyService)
    {
        $settings = CompanySetting::getSettings();
        $categories = \App\Models\PropertyCategory::where('status', 'active')->get();
        $priceRangeOptions = $currencyService->getPriceRangeOptions();
        
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

        if ($request->filled('price_range') || $request->filled('min_price') || $request->filled('max_price')) {
            $bounds = $currencyService->getFilterIdrBounds(
                $request->price_range,
                $request->filled('min_price') ? (float) $request->min_price : null,
                $request->filled('max_price') ? (float) $request->max_price : null
            );

            $query->whereHas('properties', function ($q) use ($bounds) {
                $q->where('status', 'published');
                if ($bounds['min'] !== null && $bounds['max'] !== null) {
                    $q->whereBetween('price', [$bounds['min'], $bounds['max']]);
                } elseif ($bounds['min'] !== null) {
                    $q->where('price', '>=', $bounds['min']);
                } elseif ($bounds['max'] !== null) {
                    $q->where('price', '<=', $bounds['max']);
                }
            });
        }

        $locations = $query->orderByDesc('is_popular')
                           ->orderByDesc('properties_count')
                           ->orderBy('name')
                           ->get();
        $allLocations = Location::where('status', 'active')->withCount(['properties' => function ($q) {
            $q->where('status', 'published');
        }])->get();
        $totalLocations = Location::where('status', 'active')->count();
        $totalProperties = Property::where('status', 'published')->count();

        return view('public.locations.index', compact('locations', 'allLocations', 'totalLocations', 'totalProperties', 'settings', 'categories', 'priceRangeOptions'));
    }

    public function show(string $slug, Request $request, CurrencyService $currencyService)
    {
        $settings = CompanySetting::getSettings();
        $location = Location::where('slug', $slug)->where('status', 'active')->firstOrFail();

        $query = Property::with(['category', 'location', 'images'])
            ->where('location_id', $location->id)
            ->where('status', 'published');

        if ($request->filled('type')) {
            $query->whereHas('category', function ($qc) use ($request) {
                $qc->where('slug', $request->type)
                   ->orWhere('id', $request->type);
            });
        }

        $properties = $query->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = \App\Models\PropertyCategory::where('status', 'active')->orderBy('name')->get();

        $otherLocations = Location::where('status', 'active')
            ->where('id', '!=', $location->id)
            ->orderByDesc('is_popular')
            ->orderBy('name')
            ->take(5)
            ->get();

        return view('public.locations.show', compact('location', 'properties', 'otherLocations', 'categories', 'settings'));
    }
}
