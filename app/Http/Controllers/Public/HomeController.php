<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\Benefit;
use App\Models\Statistic;
use App\Models\CmsContent;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $settings = CompanySetting::getSettings();
        $hero = CmsContent::getContent('homepage', 'hero', [
            'heading' => 'Discover Premier Luxury Real Estate in Beautiful North Bali',
            'subheading' => 'Explore beachfront luxury villas, ocean view land plots, and prime investments in Lovina, Temukus, and Singaraja.',
            'background_image' => 'cms/hero-bg.jpg',
        ]);

        $featuredProperties = Property::with(['category', 'location', 'images'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->take(3)
            ->get();

        $latestProperties = Property::with(['category', 'location', 'images'])
            ->where('status', 'published')
            ->latest()
            ->take(6)
            ->get();

        $categories = PropertyCategory::where('status', 'active')->withCount(['properties' => function ($q) {
            $q->where('status', 'published');
        }])->get();

        $popularLocations = Location::where('status', 'active')
            ->where('is_popular', true)
            ->get();

        $benefits = Benefit::where('page', 'homepage')->orderBy('sort_order', 'asc')->get();
        $statistics = Statistic::where('page', 'homepage')->where('is_visible', true)->orderBy('sort_order', 'asc')->get();
        $cta = CmsContent::getContent('homepage', 'cta');

        return view('public.home', compact(
            'settings',
            'hero',
            'featuredProperties',
            'latestProperties',
            'categories',
            'popularLocations',
            'benefits',
            'statistics',
            'cta'
        ));
    }
}
