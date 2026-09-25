<?php

// Tara handles homepage content here.

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\Benefit;
use App\Models\Statistic;
use App\Models\CmsContent;
use App\Models\CompanySetting;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(CurrencyService $currencyService)
    {
        $settings = CompanySetting::getSettings();
        $priceRangeOptions = $currencyService->getPriceRangeOptions();
        $hero = CmsContent::getContent('homepage', 'hero', [
            'heading' => 'Welcome to North Bali Real Estate Agency',
            'subheading' => 'If your dream is to live in beautiful North Bali, we can help that dream come true.',
            'background_image' => null,
        ]);

        $featuredProperties = Property::with(['category', 'categories', 'location', 'images'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->take(6)
            ->get();

        $latestSection = CmsContent::getContent('homepage', 'latest', [
            'enabled' => true,
            'section_title' => 'Latest Added Properties',
            'display_count' => 6,
        ]);

        $latestCount = max(1, min(12, (int)($latestSection['display_count'] ?? 6)));

        $latestProperties = Property::with(['category', 'categories', 'location', 'images'])
            ->where('status', 'published')
            ->latest()
            ->take($latestCount)
            ->get();

        $categories = PropertyCategory::where('status', 'active')->withCount(['properties' => function ($q) {
            $q->where('status', 'published');
        }])->get()->sortBy(function ($c) {
            $order = ['villa' => 1, 'house' => 2, 'rent' => 3, 'land' => 4, 'restaurant' => 5, 'bar' => 6, 'hotel' => 7];
            return $order[strtolower($c->slug)] ?? 99;
        })->values();

        $popularLocations = Location::where('status', 'active')
            ->where('is_popular', true)
            ->get();

        $allLocations = Location::where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        $benefits = Benefit::where('page', 'homepage')->orderBy('sort_order', 'asc')->get();
        $dbStats = Statistic::where('is_visible', true)->orderBy('sort_order', 'asc')->get();
        if ($dbStats->isNotEmpty()) {
            $statistics = $dbStats;
        } else {
            $statsSection = CmsContent::getContent('homepage', 'stats', [
                'items' => [
                    ['number' => '120+', 'label' => 'Carefully curated properties across North Bali.', 'icon' => 'Properties Listed', 'enabled' => true],
                    ['number' => '3+', 'label' => 'Proudly serving North Bali since 2023.', 'icon' => 'Years Established', 'enabled' => true],
                    ['number' => '90%+', 'label' => 'Our clients’ satisfaction is our top priority.', 'icon' => 'Customer Satisfaction', 'enabled' => true],
                ]
            ]);
            $statistics = collect($statsSection['items'] ?? [])
                ->where('enabled', true)
                ->map(function ($item) {
                    return (object) [
                        'number' => $item['number'],
                        'label' => $item['label'],
                        'icon' => $item['icon'] ?? '',
                    ];
                });
        }
        $featuredSection = CmsContent::getContent('homepage', 'featured', [
            'section_title' => 'Featured North Bali Properties',
        ]);
        $categoriesSection = CmsContent::getContent('homepage', 'categories', [
            'enabled' => true,
            'heading' => 'Explore Property Categories',
            'description' => 'Find your perfect real estate match by category in North Bali.',
        ]);
        $locationsSection = CmsContent::getContent('homepage', 'locations', [
            'enabled' => true,
            'heading' => 'Popular Locations in North Bali',
            'description' => 'Prime coastal & mountain regions in Buleleng Regency.',
        ]);
        $whyChooseSection = CmsContent::getContent('homepage', 'why_choose', [
            'heading' => 'Why Choose PT Lovina North Bali',
            'description' => 'Your trusted local partner for smooth real estate acquisitions.',
        ]);

        $cta = CmsContent::getContent('homepage', 'cta', [
            'enabled' => true,
            'heading' => 'Ready to Find Your Dream Property in North Bali?',
            'description' => 'Speak directly with our experienced property advisors today and schedule a private villa inspection.',
            'button_text' => 'Contact Us Today',
            'button_link' => '/contact',
        ]);

        $searchSection = CmsContent::getContent('homepage', 'search', [
            'enabled' => true,
            'placeholder' => 'Search Location / Property Name...',
            'filter_type' => true,
            'filter_location' => false,
            'filter_price' => true,
        ]);

        return view('public.home', compact(
            'settings',
            'hero',
            'searchSection',
            'featuredSection',
            'latestSection',
            'categoriesSection',
            'locationsSection',
            'whyChooseSection',
            'featuredProperties',
            'latestProperties',
            'categories',
            'popularLocations',
            'allLocations',
            'benefits',
            'statistics',
            'cta',
            'priceRangeOptions'
        ));
    }
}
