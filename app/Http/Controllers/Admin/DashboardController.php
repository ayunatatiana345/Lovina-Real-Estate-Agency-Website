<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Inquiry;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $settings = CompanySetting::getSettings();
        
        $totalProperties = Property::count();
        $publishedProperties = Property::where('status', 'published')->count();
        $draftProperties = Property::where('status', 'draft')->count();

        $totalInquiries = Inquiry::count();
        $newInquiries = Inquiry::where('status', 'new')->count();
        $readInquiries = Inquiry::whereIn('status', ['in_progress', 'responded'])->count();
        $repliedInquiries = Inquiry::where('status', 'closed')->count();

        $totalViews = Property::sum('views_count');
        $uniqueVisitors = 1256; // Stat simulation matching mockup

        $recentInquiries = Inquiry::with('property')
            ->latest()
            ->take(5)
            ->get();

        $topProperties = Property::with(['location', 'category'])
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'settings',
            'totalProperties',
            'publishedProperties',
            'draftProperties',
            'totalInquiries',
            'newInquiries',
            'readInquiries',
            'repliedInquiries',
            'totalViews',
            'uniqueVisitors',
            'recentInquiries',
            'topProperties'
        ));
    }
}
