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
        ]);
        $featuredSection['selected_ids'] = Property::where('is_featured', true)->pluck('id')->toArray();

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
        ]);
        $locationsSection['selected_ids'] = Location::where('is_popular', true)->pluck('id')->toArray();

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
        $bannerDefault = [
            'title' => 'About PT Lovina North Bali',
            'subtitle' => 'Your trusted real estate partner in North Bali. Established in 2023.',
            'image' => null,
            'breadcrumb' => 'Home / About Us',
        ];
        $aboutBanner = array_merge($bannerDefault, CmsContent::getContent('about_us', 'banner', []));

        $storyDefault = [
            'label' => 'OUR STORY',
            'heading' => 'Our Story',
            'description' => 'Established in 2023, PT Lovina North Bali Real Estate Agency has established itself as a dedicated property agency serving North Bali. We specialize in selecting existing villas, houses, hotels, and restaurants to offer you the best options available in beautiful North Bali.',
            'image' => null,
        ];
        $aboutStory = array_merge($storyDefault, CmsContent::getContent('about_us', 'story', []));

        $realEstateDefault = [
            'title' => 'Real Estate',
            'paragraph_1' => 'We are constantly busy with selecting existing villas, houses, hotels, and restaurants, so we can offer you the best of the best of what is available here in beautiful North Bali. We have for each his own, from wonderful big villas on the beach, houses with nice views in the mountains, till small houses in the villages for the real Bali feeling.',
            'paragraph_2' => 'On request we can also specifically search for you. Come in and visit us in our office, tell us what you are looking for and what your wishes are, and we will find your dreamhouse specially for you.',
            'paragraph_3' => 'In the rare circumstances that we can’t find anything that meets all your wishes, then we have our other specialty.',
        ];
        $aboutRealEstate = array_merge($realEstateDefault, CmsContent::getContent('about_us', 'real_estate', []));

        $andFurtherDefault = [
            'title' => 'And further',
            'description' => 'Maybe you have a villa, but you are not always in Bali, or you rent it out, then we can offer you a tailored maintenance package. We can also make sure that your villa and/or garden will always look the best that it can be, and if you receive guests, then someone of our team is there to welcome them. For the perfect first impression. Tell us your specific wishes and we will figure it out together.',
        ];
        $aboutAndFurther = array_merge($andFurtherDefault, CmsContent::getContent('about_us', 'and_further', []));

        $visionDefault = [
            'title' => 'Our Vision',
            'description' => 'To be the most trusted and transparent real estate agency in North Bali, connecting discerning buyers with exceptional lifestyle and investment properties.',
            'icon' => 'eye',
        ];
        $aboutVision = array_merge($visionDefault, CmsContent::getContent('about_us', 'vision', []));

        $missionDefault = [
            'title' => 'Our Mission',
            'description' => 'Providing the best real estate solutions for our clients with uncompromised integrity.',
            'points' => [
                'Deliver uncompromised legal integrity and title verification for every transaction.',
                'Provide personalized consultation tailored to international buyer requirements.',
                'Promote sustainable, community-respecting property developments across Buleleng Regency.',
            ]
        ];
        $aboutMission = array_merge($missionDefault, CmsContent::getContent('about_us', 'mission', []));

        $whyChooseDefault = [
            'mode' => 'use_homepage', // use_homepage or custom
            'heading' => 'Why International Buyers Trust Us',
            'custom_benefits' => [],
        ];
        $aboutWhyChoose = array_merge($whyChooseDefault, CmsContent::getContent('about_us', 'why_choose', []));

        $statsDefault = [
            'show_homepage_stats' => true,
        ];
        $aboutStats = array_merge($statsDefault, CmsContent::getContent('about_us', 'stats', []));

        // Dynamic lists for selection & previews
        $allProperties = Property::where('status', 'published')->get();
        $featuredProperties = Property::where('status', 'published')->where('is_featured', true)->take(6)->get();
        $latestProperties = Property::where('status', 'published')->latest()->take(6)->get();

        $allLocations = Location::where('status', 'active')->get();
        $popularLocations = Location::where('status', 'active')->where('is_popular', true)->get();

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
        $rawSection = $request->input('section', 'all');
        $targetSection = str_replace(['sec-home-', 'sec-'], '', $rawSection);

        $sectionMap = [
            'hero' => 'hero',
            'search' => 'search',
            'featured' => 'featured',
            'latest' => 'latest',
            'categories' => 'categories',
            'locations' => 'locations',
            'why' => 'why_choose',
            'why_choose' => 'why_choose',
            'why-choose' => 'why_choose',
            'stats' => 'stats',
            'cta' => 'cta',
            'all' => 'all',
        ];

        $targetSection = $sectionMap[$targetSection] ?? 'all';

        // Scoped validation rules per target section
        $rules = [];
        if ($targetSection === 'hero' || $targetSection === 'all') {
            $rules['hero_heading'] = 'required|string|max:255';
            $rules['hero_subheading'] = 'required|string';
            $rules['hero_small_title'] = 'nullable|string|max:255';
            $rules['hero_bg'] = 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120';
        }
        if ($targetSection === 'search' || $targetSection === 'all') {
            $rules['search_placeholder'] = 'nullable|string|max:255';
        }
        if ($targetSection === 'featured' || $targetSection === 'all') {
            $rules['featured_title'] = 'nullable|string|max:255';
            $rules['featured_ids'] = 'nullable|array';
        }
        if ($targetSection === 'latest' || $targetSection === 'all') {
            $rules['latest_title'] = 'nullable|string|max:255';
            $rules['latest_count'] = 'nullable|integer|min:1|max:20';
        }
        if ($targetSection === 'categories' || $targetSection === 'all') {
            $rules['categories_heading'] = 'nullable|string|max:255';
            $rules['categories_description'] = 'nullable|string';
            $rules['categories_count'] = 'nullable|integer|min:1|max:20';
        }
        if ($targetSection === 'locations' || $targetSection === 'all') {
            $rules['locations_heading'] = 'nullable|string|max:255';
            $rules['locations_description'] = 'nullable|string';
            $rules['popular_location_ids'] = 'nullable|array';
        }
        if ($targetSection === 'why_choose' || $targetSection === 'all') {
            $rules['why_label'] = 'nullable|string|max:100';
            $rules['why_heading'] = 'nullable|string|max:255';
            $rules['why_description'] = 'nullable|string';
        }
        if ($targetSection === 'stats' || $targetSection === 'all') {
            $rules['stat_numbers.*'] = 'nullable|string|max:100';
            $rules['stat_labels.*'] = 'nullable|string|max:255';
            $rules['stat_icons.*'] = 'nullable|string|max:100';
        }
        if ($targetSection === 'cta' || $targetSection === 'all') {
            $rules['cta_heading'] = 'required|string|max:255';
            $rules['cta_description'] = 'required|string';
            $rules['cta_button_text'] = 'required|string|max:100';
            $rules['cta_button_link'] = 'nullable|string|max:255';
        }

        try {
            $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. Please check the input fields.',
                    'errors' => $e->errors(),
                ], 422);
            }
            return redirect()->back()->withInput()->withErrors($e->errors())->with('error', $e->validator->errors()->first());
        }

        // 1. Hero Section
        if ($targetSection === 'hero' || $targetSection === 'all') {
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
        }

        // 2. Search Section
        if ($targetSection === 'search' || $targetSection === 'all') {
            CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'search'], [
                'content' => [
                    'enabled' => $request->has('search_enabled'),
                    'placeholder' => $request->input('search_placeholder', 'Search Location / Property Name...'),
                    'filter_type' => $request->has('search_filter_type'),
                    'filter_location' => $request->has('search_filter_location'),
                    'filter_price' => $request->has('search_filter_price'),
                ]
            ]);
        }

        // 3. Featured Section
        if ($targetSection === 'featured' || $targetSection === 'all') {
            $featuredIds = array_filter(array_map('intval', $request->input('featured_ids', [])));
            if (count($featuredIds) > 6) {
                $errorMsg = 'Maximum of 6 featured properties reached. Please unfeature an existing property before selecting another.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $errorMsg], 422);
                }
                return redirect()->back()->withInput()->with('error', $errorMsg);
            }

            CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'featured'], [
                'content' => [
                    'section_title' => $request->input('featured_title', 'Featured North Bali Properties'),
                    'selected_ids' => $featuredIds,
                ]
            ]);

            // Update featured flag on properties database table
            Property::query()->update(['is_featured' => false]);
            if (!empty($featuredIds)) {
                Property::whereIn('id', $featuredIds)->update(['is_featured' => true]);
            }
        }

        // 4. Latest Section
        if ($targetSection === 'latest' || $targetSection === 'all') {
            CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'latest'], [
                'content' => [
                    'enabled' => $request->has('latest_enabled'),
                    'section_title' => $request->input('latest_title', 'Latest Added Properties'),
                    'display_count' => (int) $request->input('latest_count', 6),
                ]
            ]);
        }

        // 5. Categories Section
        if ($targetSection === 'categories' || $targetSection === 'all') {
            CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'categories'], [
                'content' => [
                    'enabled' => $request->has('categories_enabled'),
                    'heading' => $request->input('categories_heading', 'Explore Property Categories'),
                    'description' => $request->input('categories_description', ''),
                    'display_count' => (int) $request->input('categories_count', 6),
                ]
            ]);
        }

        // 6. Popular Locations Section
        if ($targetSection === 'locations' || $targetSection === 'all') {
            $popularLocationIds = array_filter(array_map('intval', $request->input('popular_location_ids', [])));

            CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'locations'], [
                'content' => [
                    'enabled' => $request->has('locations_enabled'),
                    'heading' => $request->input('locations_heading', 'Popular Locations in North Bali'),
                    'description' => $request->input('locations_description', ''),
                    'selected_ids' => $popularLocationIds,
                ]
            ]);

            Location::query()->update(['is_popular' => false]);
            if (!empty($popularLocationIds)) {
                Location::whereIn('id', $popularLocationIds)->update(['is_popular' => true]);
            }
        }

        // 7. Why Choose Us Section
        if ($targetSection === 'why_choose' || $targetSection === 'all') {
            CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'why_choose'], [
                'content' => [
                    'section_label' => $request->input('why_label', 'WHY CHOOSE US'),
                    'heading' => $request->input('why_heading', 'Why Choose PT Lovina North Bali'),
                    'description' => $request->input('why_description', ''),
                ]
            ]);
        }

        // 8. Company Statistics Section
        if ($targetSection === 'stats' || $targetSection === 'all') {
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
        }

        // 9. Contact CTA Section
        if ($targetSection === 'cta' || $targetSection === 'all') {
            CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'cta'], [
                'content' => [
                    'enabled' => $request->has('cta_enabled'),
                    'heading' => $request->input('cta_heading', 'Ready to Find Your Dream Property in North Bali?'),
                    'description' => $request->input('cta_description', ''),
                    'button_text' => $request->input('cta_button_text', 'Contact Us Today'),
                    'button_link' => $request->input('cta_button_link', '/contact'),
                ]
            ]);
        }

        $activeSection = $request->input('active_section', 'sec-hero');
        if ($rawSection !== 'all' && str_starts_with($rawSection, 'sec-')) {
            $activeSection = $rawSection;
        }

        $successMessage = ($targetSection === 'all')
            ? 'All Homepage sections saved successfully.'
            : 'Homepage section saved successfully.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage
            ]);
        }

        return redirect()->route('admin.cms.index', ['tab' => 'homepage', 'section' => $activeSection])
            ->with('success', $successMessage);
    }

    public function updateAbout(Request $request)
    {
        $rawSection = $request->input('section', 'all');
        $targetSection = str_replace(['sec-ab-', 'sec-'], '', $rawSection);

        // Map section names
        $sectionMap = [
            'banner' => 'banner',
            'story' => 'story',
            'real_estate' => 'real_estate',
            'real-estate' => 'real_estate',
            'and_further' => 'and_further',
            'and-further' => 'and_further',
            'vision' => 'vision',
            'mission' => 'mission',
            'why' => 'why_choose',
            'why_choose' => 'why_choose',
            'why-choose' => 'why_choose',
            'stats' => 'stats',
            'all' => 'all',
        ];

        $targetSection = $sectionMap[$targetSection] ?? 'all';

        // Validation rules per section
        $rules = [];
        if ($targetSection === 'banner' || $targetSection === 'all') {
            $rules['banner_title'] = 'nullable|string|max:255';
            $rules['banner_subtitle'] = 'nullable|string|max:500';
            $rules['banner_breadcrumb'] = 'nullable|string|max:255';
            $rules['banner_image'] = 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120';
        }
        if ($targetSection === 'story' || $targetSection === 'all') {
            $rules['story_label'] = 'nullable|string|max:100';
            $rules['story_heading'] = 'nullable|string|max:255';
            $rules['story_description'] = 'nullable|string';
            $rules['story_image'] = 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120';
        }
        if ($targetSection === 'real_estate' || $targetSection === 'all') {
            $rules['real_estate_title'] = 'nullable|string|max:255';
            $rules['real_estate_p1'] = 'nullable|string';
            $rules['real_estate_p2'] = 'nullable|string';
            $rules['real_estate_p3'] = 'nullable|string';
        }
        if ($targetSection === 'and_further' || $targetSection === 'all') {
            $rules['and_further_title'] = 'nullable|string|max:255';
            $rules['and_further_desc'] = 'nullable|string';
        }
        if ($targetSection === 'vision' || $targetSection === 'all') {
            $rules['vision_title'] = 'nullable|string|max:255';
            $rules['vision_description'] = 'nullable|string';
            $rules['vision_icon'] = 'nullable|string|max:50';
        }
        if ($targetSection === 'mission' || $targetSection === 'all') {
            $rules['mission_title'] = 'nullable|string|max:255';
            $rules['mission_description'] = 'nullable|string';
            $rules['mission_points'] = 'nullable|array';
            $rules['mission_points.*'] = 'nullable|string|max:500';
        }
        if ($targetSection === 'why_choose' || $targetSection === 'all') {
            $rules['about_why_mode'] = 'nullable|string|in:use_homepage,custom';
            $rules['why_heading'] = 'nullable|string|max:255';
        }

        try {
            $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. Please check the input fields.',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        $savedData = [];
        $savedImageUrls = [];

        // A. Page Banner
        if ($targetSection === 'banner' || $targetSection === 'all') {
            $bannerData = CmsContent::getContent('about_us', 'banner', []);
            if ($request->has('banner_title')) {
                $bannerData['title'] = $request->input('banner_title', 'About PT Lovina North Bali');
            }
            if ($request->has('banner_subtitle')) {
                $bannerData['subtitle'] = $request->input('banner_subtitle', '');
            }
            if ($request->has('banner_breadcrumb')) {
                $bannerData['breadcrumb'] = $request->input('banner_breadcrumb', 'Home / About Us');
            }
            if ($request->hasFile('banner_image')) {
                $bannerData['image'] = $request->file('banner_image')->store('cms', 'public');
            }
            CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'banner'], ['content' => $bannerData]);
            $savedData['banner'] = $bannerData;
            if (!empty($bannerData['image'])) {
                $savedImageUrls['banner_image'] = asset('storage/' . $bannerData['image']);
            }
        }

        // B. Company Story
        if ($targetSection === 'story' || $targetSection === 'all') {
            $storyData = CmsContent::getContent('about_us', 'story', []);
            if ($request->has('story_label')) {
                $storyData['label'] = $request->input('story_label', 'OUR STORY');
            }
            if ($request->has('story_heading')) {
                $storyData['heading'] = $request->input('story_heading', 'Our Story');
            }
            if ($request->has('story_description')) {
                $storyData['description'] = $request->input('story_description', '');
            }
            if ($request->hasFile('story_image')) {
                $storyData['image'] = $request->file('story_image')->store('cms', 'public');
            }
            CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'story'], ['content' => $storyData]);
            $savedData['story'] = $storyData;
            if (!empty($storyData['image'])) {
                $savedImageUrls['story_image'] = asset('storage/' . $storyData['image']);
            }
        }

        // C. Real Estate Section
        if ($targetSection === 'real_estate' || $targetSection === 'all') {
            $realEstateData = CmsContent::getContent('about_us', 'real_estate', []);
            if ($request->has('real_estate_title')) {
                $realEstateData['title'] = $request->input('real_estate_title', 'Real Estate');
            }
            if ($request->has('real_estate_p1')) {
                $realEstateData['paragraph_1'] = $request->input('real_estate_p1', '');
            }
            if ($request->has('real_estate_p2')) {
                $realEstateData['paragraph_2'] = $request->input('real_estate_p2', '');
            }
            if ($request->has('real_estate_p3')) {
                $realEstateData['paragraph_3'] = $request->input('real_estate_p3', '');
            }
            CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'real_estate'], ['content' => $realEstateData]);
            $savedData['real_estate'] = $realEstateData;
        }

        // D. And Further Section
        if ($targetSection === 'and_further' || $targetSection === 'all') {
            $andFurtherData = CmsContent::getContent('about_us', 'and_further', []);
            if ($request->has('and_further_title')) {
                $andFurtherData['title'] = $request->input('and_further_title', 'And further');
            }
            if ($request->has('and_further_desc')) {
                $andFurtherData['description'] = $request->input('and_further_desc', '');
            }
            CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'and_further'], ['content' => $andFurtherData]);
            $savedData['and_further'] = $andFurtherData;
        }

        // E. Vision
        if ($targetSection === 'vision' || $targetSection === 'all') {
            $visionData = CmsContent::getContent('about_us', 'vision', []);
            if ($request->has('vision_title')) {
                $visionData['title'] = $request->input('vision_title', 'Our Vision');
            }
            if ($request->has('vision_description')) {
                $visionData['description'] = $request->input('vision_description', '');
            }
            if ($request->has('vision_icon')) {
                $visionData['icon'] = $request->input('vision_icon', 'eye');
            }
            CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'vision'], ['content' => $visionData]);
            $savedData['vision'] = $visionData;
        }

        // F. Mission
        if ($targetSection === 'mission' || $targetSection === 'all') {
            $missionData = CmsContent::getContent('about_us', 'mission', []);
            if ($request->has('mission_title')) {
                $missionData['title'] = $request->input('mission_title', 'Our Mission');
            }
            if ($request->has('mission_description')) {
                $missionData['description'] = $request->input('mission_description', '');
            }
            if ($request->has('mission_points')) {
                $missionPoints = array_values(array_filter($request->input('mission_points', [])));
                $missionData['points'] = $missionPoints;
            }
            CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'mission'], ['content' => $missionData]);
            $savedData['mission'] = $missionData;
        }

        // G. Why Choose Us Mode
        if ($targetSection === 'why_choose' || $targetSection === 'all') {
            $whyData = CmsContent::getContent('about_us', 'why_choose', []);
            if ($request->has('about_why_mode')) {
                $whyData['mode'] = $request->input('about_why_mode', 'use_homepage');
            }
            if ($request->has('why_heading')) {
                $whyData['heading'] = $request->input('why_heading', 'Why International Buyers Trust Us');
            }
            CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'why_choose'], ['content' => $whyData]);
            $savedData['why_choose'] = $whyData;
        }

        // H. Company Statistics Toggle
        if ($targetSection === 'stats' || $targetSection === 'all') {
            $statsData = CmsContent::getContent('about_us', 'stats', []);
            $statsData['show_homepage_stats'] = $request->has('about_show_stats');
            CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'stats'], ['content' => $statsData]);
            $savedData['stats'] = $statsData;
        }

        $activeSection = 'sec-ab-' . ($targetSection === 'all' ? 'banner' : str_replace('_', '-', $targetSection));

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => $targetSection === 'all' ? 'All changes saved successfully.' : 'Section saved successfully.',
                'section' => $rawSection,
                'target_section' => $targetSection,
                'active_section' => $activeSection,
                'image_urls' => $savedImageUrls,
                'data' => $savedData,
            ]);
        }

        return redirect()->route('admin.cms.index', ['tab' => 'about', 'section' => $activeSection])
            ->with('success', 'Changes saved successfully.');
    }
}
