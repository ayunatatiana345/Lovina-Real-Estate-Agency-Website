<?php

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
        $story = CmsContent::getContent('about_us', 'story', [
            'title' => 'Our Story',
            'description' => 'Established in 2023, PT Lovina North Bali Real Estate Agency has established itself as a dedicated property agency serving North Bali. We specialize in selecting existing villas, houses, hotels, and restaurants to offer you the best options available in beautiful North Bali.',
            'vision' => 'To be the most trusted and transparent real estate agency in North Bali, connecting discerning buyers with exceptional lifestyle and investment properties.',
            'mission' => [
                'Provide personalized consultation tailored to international buyer requirements.',
                'Promote sustainable, community-respecting property developments across Buleleng Regency.',
            ],
        ]);

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

        return view('public.about', compact('settings', 'story', 'benefits', 'statistics'));
    }
}
