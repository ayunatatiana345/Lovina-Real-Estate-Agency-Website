<?php

// Tara handles about us page here.

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Benefit;
use App\Models\Statistic;
use App\Models\CmsContent;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $settings = CompanySetting::getSettings();

        $banner = CmsContent::getContent('about_us', 'banner', [
            'title' => 'About PT Lovina North Bali',
            'subtitle' => 'Your trusted real estate partner in North Bali. Established in 2023.',
            'breadcrumb' => 'Home / About Us',
        ]);

        $story = CmsContent::getContent('about_us', 'story', [
            'heading' => 'Our Story',
            'description' => 'Established in 2023, PT Lovina North Bali Real Estate Agency has established itself as a dedicated property agency serving North Bali. We specialize in selecting existing villas, houses, hotels, and restaurants to offer you the best options available in beautiful North Bali.',
        ]);

        $realEstate = CmsContent::getContent('about_us', 'real_estate', [
            'title' => 'Real Estate',
            'paragraph_1' => 'We are constantly busy with selecting existing villas, houses, hotels, and restaurants, so we can offer you the best of the best of what is available here in beautiful North Bali. We have for each his own, from wonderful big villas on the beach, houses with nice views in the mountains, till small houses in the villages for the real Bali feeling.',
            'paragraph_2' => 'On request we can also specifically search for you. Come in and visit us in our office, tell us what you are looking for and what your wishes are, and we will find your dreamhouse specially for you.',
            'paragraph_3' => 'In the rare circumstances that we can’t find anything that meets all your wishes, then we have our other specialty.',
        ]);

        $andFurther = CmsContent::getContent('about_us', 'and_further', [
            'title' => 'And further',
            'description' => 'Maybe you have a villa, but you are not always in Bali, or you rent it out, then we can offer you a tailored maintenance package. We can also make sure that your villa and/or garden will always look the best that it can be, and if you receive guests, then someone of our team is there to welcome them. For the perfect first impression. Tell us your specific wishes and we will figure it out together.',
        ]);

        $vision = CmsContent::getContent('about_us', 'vision', [
            'title' => 'Our Vision',
            'description' => 'To be the most trusted and transparent real estate agency in North Bali, connecting discerning buyers with exceptional lifestyle and investment properties.',
        ]);

        $mission = CmsContent::getContent('about_us', 'mission', [
            'title' => 'Our Mission',
            'points' => [
                'Deliver uncompromised legal integrity and title verification for every transaction.',
                'Provide personalized consultation tailored to international buyer requirements.',
                'Promote sustainable, community-respecting property developments across Buleleng Regency.',
            ],
        ]);

        $benefits = Benefit::where('page', 'homepage')->orderBy('sort_order', 'asc')->get();
        if ($benefits->isEmpty()) {
            $benefits = collect([
                (object) ['title' => 'North Bali Property Focus', 'description' => 'We specialize in villas, houses, land, hotels, and restaurants across beautiful North Bali.', 'icon' => 'home'],
                (object) ['title' => 'Tailored Property Search', 'description' => 'On request, we can specifically search for properties based on what you are looking for and what your wishes are.', 'icon' => 'search'],
                (object) ['title' => 'Local Property Support', 'description' => 'We can help you find a property that suits your requirements and provide support based on your specific needs.', 'icon' => 'shield'],
            ]);
        }

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

        return view('public.about', compact('settings', 'banner', 'story', 'realEstate', 'andFurther', 'vision', 'mission', 'benefits', 'statistics'));
    }
}
