<?php

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
        
        $hero = CmsContent::getContent('homepage', 'hero');
        $cta = CmsContent::getContent('homepage', 'cta');
        $story = CmsContent::getContent('about_us', 'story');

        $featuredProperties = Property::where('is_featured', true)->get();
        $allProperties = Property::where('status', 'published')->get();
        $popularLocations = Location::where('is_popular', true)->get();

        $benefits = Benefit::where('page', $tab === 'about' ? 'about' : 'homepage')->orderBy('sort_order', 'asc')->get();
        $statistics = Statistic::where('page', 'homepage')->orderBy('sort_order', 'asc')->get();
        $categories = PropertyCategory::all();

        return view('admin.cms.index', compact(
            'tab',
            'settings',
            'hero',
            'cta',
            'story',
            'featuredProperties',
            'allProperties',
            'popularLocations',
            'benefits',
            'statistics',
            'categories'
        ));
    }

    public function updateHomepage(Request $request)
    {
        $data = $request->validate([
            'hero_heading' => 'required|string',
            'hero_subheading' => 'required|string',
            'cta_heading' => 'required|string',
            'cta_description' => 'required|string',
            'cta_button_text' => 'required|string',
        ]);

        $heroContent = CmsContent::getContent('homepage', 'hero');
        if ($request->hasFile('hero_bg')) {
            $path = $request->file('hero_bg')->store('cms', 'public');
            $heroContent['background_image'] = $path;
        }
        $heroContent['heading'] = $data['hero_heading'];
        $heroContent['subheading'] = $data['hero_subheading'];

        CmsContent::updateOrCreate(
            ['page' => 'homepage', 'section_key' => 'hero'],
            ['content' => $heroContent]
        );

        CmsContent::updateOrCreate(
            ['page' => 'homepage', 'section_key' => 'cta'],
            ['content' => [
                'heading' => $data['cta_heading'],
                'description' => $data['cta_description'],
                'button_text' => $data['cta_button_text'],
                'button_link' => '/contact',
            ]]
        );

        return redirect()->route('admin.cms.index', ['tab' => 'homepage'])
            ->with('success', 'Homepage content updated successfully!');
    }

    public function updateAbout(Request $request)
    {
        $data = $request->validate([
            'story_title' => 'required|string',
            'story_description' => 'required|string',
            'vision' => 'required|string',
            'mission_1' => 'nullable|string',
            'mission_2' => 'nullable|string',
            'mission_3' => 'nullable|string',
        ]);

        $missions = array_filter([$data['mission_1'] ?? null, $data['mission_2'] ?? null, $data['mission_3'] ?? null]);

        CmsContent::updateOrCreate(
            ['page' => 'about_us', 'section_key' => 'story'],
            ['content' => [
                'title' => $data['story_title'],
                'description' => $data['story_description'],
                'vision' => $data['vision'],
                'mission' => $missions,
            ]]
        );

        return redirect()->route('admin.cms.index', ['tab' => 'about'])
            ->with('success', 'About Us content updated successfully!');
    }
}
