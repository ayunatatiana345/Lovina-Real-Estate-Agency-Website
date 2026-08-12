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
            'heading' => 'Welcome to North Bali Real Estate Agency',
            'subheading' => 'If your dream is to live in beautiful North Bali, we can help that dream come true.',
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
