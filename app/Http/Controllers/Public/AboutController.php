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
            'description' => 'Founded in Lovina, PT Lovina North Bali Real Estate Agency has established itself as the leading property agency dedicated to North Bali real estate.',
            'vision' => 'To be the most trusted and transparent real estate agency in North Bali.',
            'mission' => [
                'Deliver uncompromised legal integrity and title verification for every transaction.',
                'Provide personalized consultation tailored to international buyer requirements.',
                'Promote sustainable property developments across Buleleng Regency.',
            ],
        ]);

        $benefits = Benefit::where('page', 'homepage')->orderBy('sort_order', 'asc')->get();
        $statistics = Statistic::where('page', 'homepage')->where('is_visible', true)->orderBy('sort_order', 'asc')->get();

        return view('public.about', compact('settings', 'story', 'benefits', 'statistics'));
    }
}
