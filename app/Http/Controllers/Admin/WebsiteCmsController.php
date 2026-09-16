<?php

// Tara handles website CMS here.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsContent;
use App\Models\Benefit;
use App\Models\Statistic;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteCmsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'homepage');
        $settings = CompanySetting::getSettings();

        // 1. Homepage Sections
        $hero = CmsContent::getContent('homepage', 'hero', [
            'enabled' => true,
            'background_image' => 'cms/hero-bg.jpg',
            'small_title' => 'Find Your Dream',
            'heading' => 'Welcome to North Bali Real Estate Agency',
            'subheading' => 'If your dream is to live in beautiful North Bali, we can help that dream come true.',
            'buttons' => [
                ['text' => 'Browse Properties', 'link' => '/properties', 'style' => 'primary'],
                ['text' => 'Contact Us', 'link' => '/contact', 'style' => 'outline']
            ],
            'overlay' => 'dark',
            'overlay_opacity' => '60',
            'text_alignment' => 'left',
        ]);

        $searchSection = CmsContent::getContent('homepage', 'search', [
            'enabled' => true,
            'placeholder' => 'Search Location / Property Name...',
            'filter_type' => true,
            'filter_location' => true,
            'filter_price' => true,
        ]);

        $featuredSection = CmsContent::getContent('homepage', 'featured', [
            'section_title' => 'Featured North Bali Properties',
            'selected_ids' => Property::where('is_featured', true)->pluck('id')->take(6)->toArray(),
        ]);

        $latestSection = CmsContent::getContent('homepage', 'latest', [
            'enabled' => true,
            'section_title' => 'Latest Added Properties',
            'display_count' => 6,
        ]);

        $categoriesSection = CmsContent::getContent('homepage', 'categories', [
            'enabled' => true,
            'heading' => 'Explore Property Categories',
            'description' => 'Find your perfect real estate match by category in North Bali.',
            'display_count' => 6,
        ]);

        $locationsSection = CmsContent::getContent('homepage', 'locations', [
            'enabled' => true,
            'heading' => 'Popular Locations in North Bali',
            'description' => 'Prime coastal & mountain regions in Buleleng Regency.',
            'selected_ids' => Location::where('is_popular', true)->pluck('id')->toArray(),
        ]);

        $whyChooseSection = CmsContent::getContent('homepage', 'why_choose', [
            'section_label' => 'WHY CHOOSE US',
            'heading' => 'Why Choose PT Lovina North Bali',
            'description' => 'Your trusted local partner for smooth real estate acquisitions.',
        ]);

        $statsSection = CmsContent::getContent('homepage', 'stats', [
            'items' => [
                ['number' => '120+', 'label' => 'Carefully curated properties across North Bali.', 'icon' => 'Properties Listed', 'enabled' => true],
                ['number' => '3+', 'label' => 'Proudly serving North Bali since 2023.', 'icon' => 'Years Established', 'enabled' => true],
                ['number' => '90%+', 'label' => 'Our clients’ satisfaction is our top priority.', 'icon' => 'Customer Satisfaction', 'enabled' => true],
            ]
        ]);

        $cta = CmsContent::getContent('homepage', 'cta', [
            'enabled' => true,
            'heading' => 'Ready to Find Your Dream Property in North Bali?',
            'description' => 'Speak directly with our experienced property advisors today and schedule a private villa inspection.',
            'button_text' => 'Contact Us Today',
            'button_link' => '/contact',
        ]);

        // 2. About Us Sections
        $aboutBanner = CmsContent::getContent('about_us', 'banner', [
            'title' => 'About PT Lovina North Bali',
            'subtitle' => 'Your trusted real estate partner in North Bali. Established in 2023.',
            'image' => 'cms/about-banner.jpg',
            'breadcrumb' => 'Home / About Us',
        ]);

        $aboutStory = CmsContent::getContent('about_us', 'story', [
            'label' => 'OUR STORY',
            'heading' => 'Our Story',
            'description' => 'Established in 2023, PT Lovina North Bali Real Estate Agency has established itself as a dedicated property agency serving North Bali. We specialize in selecting existing villas, houses, hotels, and restaurants to offer you the best options available in beautiful North Bali.',
            'image' => 'images/office-building.jpg',
        ]);

        $aboutRealEstate = CmsContent::getContent('about_us', 'real_estate', [
            'title' => 'Real Estate',
            'paragraph_1' => 'We are constantly busy with selecting existing villas, houses, hotels, and restaurants, so we can offer you the best of the best of what is available here in beautiful North Bali. We have for each his own, from wonderful big villas on the beach, houses with nice views in the mountains, till small houses in the villages for the real Bali feeling.',
            'paragraph_2' => 'On request we can also specifically search for you. Come in and visit us in our office, tell us what you are looking for and what your wishes are, and we will find your dreamhouse specially for you.',
            'paragraph_3' => 'In the rare circumstances that we can’t find anything that meets all your wishes, then we have our other specialty.',
        ]);

        $aboutAndFurther = CmsContent::getContent('about_us', 'and_further', [
            'title' => 'And further',
            'description' => 'Maybe you have a villa, but you are not always in Bali, or you rent it out, then we can offer you a tailored maintenance package. We can also make sure that your villa and/or garden will always look the best that it can be, and if you receive guests, then someone of our team is there to welcome them. For the perfect first impression. Tell us your specific wishes and we will figure it out together.',
        ]);

        $aboutVision = CmsContent::getContent('about_us', 'vision', [
            'title' => 'Our Vision',
            'description' => 'To be the most trusted and transparent real estate agency in North Bali, connecting discerning buyers with exceptional lifestyle and investment properties.',
            'icon' => 'eye',
        ]);

        $aboutMission = CmsContent::getContent('about_us', 'mission', [
            'title' => 'Our Mission',
            'description' => 'Providing the best real estate solutions for our clients with uncompromised integrity.',
            'points' => [
                'Deliver uncompromised legal integrity and title verification for every transaction.',
                'Provide personalized consultation tailored to international buyer requirements.',
                'Promote sustainable, community-respecting property developments across Buleleng Regency.',
            ]
        ]);

        $aboutWhyChoose = CmsContent::getContent('about_us', 'why_choose', [
            'mode' => 'use_homepage', // use_homepage or custom
            'custom_benefits' => [],
        ]);

        $aboutStats = CmsContent::getContent('about_us', 'stats', [
            'show_homepage_stats' => true,
        ]);

        // Dynamic lists for selection & previews
        $allProperties = Property::where('status', 'published')->get();
        $featuredProperties = Property::whereIn('id', $featuredSection['selected_ids'] ?? [])->get();
        if ($featuredProperties->isEmpty()) {
            $featuredProperties = Property::where('is_featured', true)->take(3)->get();
        }
        $latestProperties = Property::where('status', 'published')->latest()->take(6)->get();

        $allLocations = Location::where('status', 'active')->get();
        $popularLocations = Location::whereIn('id', $locationsSection['selected_ids'] ?? [])->get();
        if ($popularLocations->isEmpty()) {
            $popularLocations = Location::where('is_popular', true)->get();
        }

        $benefits = Benefit::where('page', 'homepage')->orderBy('sort_order', 'asc')->get();
        $categories = PropertyCategory::where('status', 'active')->get();

        return view('admin.cms.index', compact(
            'tab',
            'settings',
            'hero',
            'searchSection',
            'featuredSection',
            'latestSection',
            'categoriesSection',
            'locationsSection',
            'whyChooseSection',
            'statsSection',
            'cta',
            'aboutBanner',
            'aboutStory',
            'aboutRealEstate',
            'aboutAndFurther',
            'aboutVision',
            'aboutMission',
            'aboutWhyChoose',
            'aboutStats',
            'allProperties',
            'featuredProperties',
            'latestProperties',
            'allLocations',
            'popularLocations',
            'benefits',
            'categories'
        ));
    }

    public function updateHomepage(Request $request)
    {
        // 1. Hero Section
        $heroData = CmsContent::getContent('homepage', 'hero');
        $heroData['enabled'] = $request->has('hero_enabled');
        $heroData['small_title'] = $request->input('hero_small_title', '');
        $heroData['heading'] = $request->input('hero_heading', $heroData['heading'] ?? '');
        $heroData['subheading'] = $request->input('hero_subheading', $heroData['subheading'] ?? '');
        $heroData['overlay'] = $request->input('hero_overlay', 'dark');
        $heroData['overlay_opacity'] = $request->input('hero_overlay_opacity', '60');
        $heroData['text_alignment'] = $request->input('hero_text_alignment', 'left');

        if ($request->hasFile('hero_bg')) {
            $heroData['background_image'] = $request->file('hero_bg')->store('cms', 'public');
        }

        if ($request->has('buttons_text')) {
            $buttons = [];
            foreach ($request->input('buttons_text') as $idx => $txt) {
                if (!empty($txt)) {
                    $buttons[] = [
                        'text' => $txt,
                        'link' => $request->input("buttons_link.{$idx}", '#'),
                        'style' => $request->input("buttons_style.{$idx}", 'primary'),
                    ];
                }
            }
            $heroData['buttons'] = $buttons;
        }

        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'hero'], ['content' => $heroData]);

        // 2. Search Section
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'search'], [
            'content' => [
                'enabled' => $request->has('search_enabled'),
                'placeholder' => $request->input('search_placeholder', 'Search Location / Property Name...'),
                'filter_type' => $request->has('search_filter_type'),
                'filter_location' => $request->has('search_filter_location'),
                'filter_price' => $request->has('search_filter_price'),
            ]
        ]);

        // 3. Featured Section
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'featured'], [
            'content' => [
                'section_title' => $request->input('featured_title', 'Featured North Bali Properties'),
                'selected_ids' => array_map('intval', $request->input('featured_ids', [])),
            ]
        ]);

        // Update featured flag on properties database table
        if ($request->has('featured_ids')) {
            Property::query()->update(['is_featured' => false]);
            Property::whereIn('id', $request->input('featured_ids'))->update(['is_featured' => true]);
        }

        // 4. Latest Section
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'latest'], [
            'content' => [
                'enabled' => $request->has('latest_enabled'),
                'section_title' => $request->input('latest_title', 'Latest Added Properties'),
                'display_count' => (int) $request->input('latest_count', 6),
            ]
        ]);

        // 5. Categories Section
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'categories'], [
            'content' => [
                'enabled' => $request->has('categories_enabled'),
                'heading' => $request->input('categories_heading', 'Explore Property Categories'),
                'description' => $request->input('categories_description', ''),
                'display_count' => (int) $request->input('categories_count', 6),
            ]
        ]);

        // 6. Popular Locations Section
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'locations'], [
            'content' => [
                'enabled' => $request->has('locations_enabled'),
                'heading' => $request->input('locations_heading', 'Popular Locations in North Bali'),
                'description' => $request->input('locations_description', ''),
                'selected_ids' => array_map('intval', $request->input('popular_location_ids', [])),
            ]
        ]);

        if ($request->has('popular_location_ids')) {
            Location::query()->update(['is_popular' => false]);
            Location::whereIn('id', $request->input('popular_location_ids'))->update(['is_popular' => true]);
        }

        // 7. Why Choose Us Section
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'why_choose'], [
            'content' => [
                'section_label' => $request->input('why_label', 'WHY CHOOSE US'),
                'heading' => $request->input('why_heading', 'Why Choose PT Lovina North Bali'),
                'description' => $request->input('why_description', ''),
            ]
        ]);

        // 8. Company Statistics Section
        if ($request->has('stat_labels')) {
            $statsItems = [];
            foreach ($request->input('stat_labels') as $idx => $label) {
                if (!empty($label)) {
                    $num = $request->input("stat_numbers.{$idx}", '100+');
                    $icon = $request->input("stat_icons.{$idx}", 'home');
                    $isEnabled = isset($request->input('stat_enabled')[$idx]);
                    $statsItems[] = [
                        'number' => $num,
                        'label' => $label,
                        'icon' => $icon,
                        'enabled' => $isEnabled,
                    ];

                    // Sync to Statistic database model if applicable
                    Statistic::updateOrCreate(
                        ['page' => 'homepage', 'sort_order' => $idx + 1],
                        [
                            'number' => $num,
                            'label' => $label,
                            'icon' => $icon,
                            'is_visible' => $isEnabled,
                        ]
                    );
                }
            }
            CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'stats'], [
                'content' => ['items' => $statsItems]
            ]);
        }

        // 9. Contact CTA Section
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'cta'], [
            'content' => [
                'enabled' => $request->has('cta_enabled'),
                'heading' => $request->input('cta_heading', 'Ready to Find Your Dream Property in North Bali?'),
                'description' => $request->input('cta_description', ''),
                'button_text' => $request->input('cta_button_text', 'Contact Us Today'),
                'button_link' => $request->input('cta_button_link', '/contact'),
            ]
        ]);

        return redirect()->route('admin.cms.index', ['tab' => 'homepage'])
            ->with('success', 'Homepage CMS updated successfully!');
    }

    public function updateAbout(Request $request)
    {
        // A. Page Banner
        $bannerData = CmsContent::getContent('about_us', 'banner');
        $bannerData['title'] = $request->input('banner_title', 'About PT Lovina North Bali');
        $bannerData['subtitle'] = $request->input('banner_subtitle', 'Your trusted real estate partner in North Bali. Established in 2023.');
        $bannerData['breadcrumb'] = $request->input('banner_breadcrumb', 'Home / About Us');
        if ($request->hasFile('banner_image')) {
            $bannerData['image'] = $request->file('banner_image')->store('cms', 'public');
        }
        CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'banner'], ['content' => $bannerData]);

        // B. Company Story
        $storyData = CmsContent::getContent('about_us', 'story');
        $storyData['label'] = $request->input('story_label', 'OUR STORY');
        $storyData['heading'] = $request->input('story_heading', 'Our Story');
        $storyData['description'] = $request->input('story_description', '');
        if ($request->hasFile('story_image')) {
            $storyData['image'] = $request->file('story_image')->store('cms', 'public');
        }
        CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'story'], ['content' => $storyData]);

        // C. Real Estate Section
        CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'real_estate'], [
            'content' => [
                'title' => $request->input('real_estate_title', 'Real Estate'),
                'paragraph_1' => $request->input('real_estate_p1', ''),
                'paragraph_2' => $request->input('real_estate_p2', ''),
                'paragraph_3' => $request->input('real_estate_p3', ''),
            ]
        ]);

        // D. And Further Section
        CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'and_further'], [
            'content' => [
                'title' => $request->input('and_further_title', 'And further'),
                'description' => $request->input('and_further_desc', ''),
            ]
        ]);

        // E. Vision
        CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'vision'], [
            'content' => [
                'title' => $request->input('vision_title', 'Our Vision'),
                'description' => $request->input('vision_description', ''),
                'icon' => $request->input('vision_icon', 'eye'),
            ]
        ]);

        // F. Mission
        $missionPoints = array_values(array_filter($request->input('mission_points', [])));
        CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'mission'], [
            'content' => [
                'title' => $request->input('mission_title', 'Our Mission'),
                'description' => $request->input('mission_description', ''),
                'points' => $missionPoints,
            ]
        ]);

        // G. Why Choose Us Mode
        CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'why_choose'], [
            'content' => [
                'mode' => $request->input('about_why_mode', 'use_homepage'),
            ]
        ]);

        // H. Company Statistics Toggle
        CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'stats'], [
            'content' => [
                'show_homepage_stats' => $request->has('about_show_stats'),
            ]
        ]);

        return redirect()->route('admin.cms.index', ['tab' => 'about'])
            ->with('success', 'About Us CMS updated successfully!');
    }
}
