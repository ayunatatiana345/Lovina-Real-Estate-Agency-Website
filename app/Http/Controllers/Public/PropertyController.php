<?php

// Aragon handles property data here.

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\CompanySetting;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request, CurrencyService $currencyService)
    {
        $settings = CompanySetting::getSettings();
        $categories = PropertyCategory::where('status', 'active')->orderBy('name', 'asc')->get();
        $locations = Location::where('status', 'active')->orderBy('name', 'asc')->get();
        $priceRangeOptions = $currencyService->getPriceRangeOptions();

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

        if ($request->filled('price_range') || $request->filled('min_price') || $request->filled('max_price')) {
            $bounds = $currencyService->getFilterIdrBounds(
                $request->price_range,
                $request->filled('min_price') ? (float) $request->min_price : null,
                $request->filled('max_price') ? (float) $request->max_price : null
            );

            if ($bounds['min'] !== null && $bounds['max'] !== null) {
                $query->whereBetween('price', [$bounds['min'], $bounds['max']]);
            } elseif ($bounds['min'] !== null) {
                $query->where('price', '>=', $bounds['min']);
            } elseif ($bounds['max'] !== null) {
                $query->where('price', '<=', $bounds['max']);
            }
        }

        if ($request->filled('date_uploaded')) {
            if ($request->date_uploaded === 'last_30_days') {
                $query->where('created_at', '>=', now()->subDays(30));
            } elseif ($request->date_uploaded === 'last_7_days') {
                $query->where('created_at', '>=', now()->subDays(7));
            }
        }

        $properties = $query->latest()->paginate(30)->withQueryString();

        return view('public.properties.index', compact('properties', 'categories', 'locations', 'settings', 'priceRangeOptions'));
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
