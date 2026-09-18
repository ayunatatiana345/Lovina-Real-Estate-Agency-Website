<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Inquiry;
use App\Models\CompanySetting;
use App\Models\Article;
use App\Models\ArticleView;
use App\Models\PageView;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $settings = CompanySetting::getSettings();
        
        // 1. Properties by Status
        $totalProperties = Property::count();
        $publishedProperties = Property::where('status', 'published')->count();
        $draftProperties = Property::where('status', 'draft')->count();

        // 2. Inquiries by Status (reconciles 100% to total)
        $totalInquiries = Inquiry::count();
        $newInquiries = Inquiry::where('status', 'new')->count();
        $readInquiries = Inquiry::where('status', 'in_progress')->count();
        $repliedInquiries = Inquiry::whereIn('status', ['responded', 'closed'])->count();

        // 3. Rolling 30-Day Windows for Traffic Analytics
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();

        $prevStartDate = now()->subDays(59)->startOfDay();
        $prevEndDate = now()->subDays(30)->endOfDay();

        // Total Views (Last 30 Days)
        $totalViews = PageView::whereBetween('created_at', [$startDate, $endDate])->count();
        $prevTotalViews = PageView::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();

        if ($prevTotalViews > 0) {
            $viewsDiff = $totalViews - $prevTotalViews;
            $viewsGrowthPercent = round(($viewsDiff / $prevTotalViews) * 100, 1);
            $viewsGrowthText = ($viewsGrowthPercent >= 0 ? "↑ " : "↓ ") . abs($viewsGrowthPercent) . "% vs previous 30 days";
            $viewsGrowthPositive = $viewsGrowthPercent >= 0;
        } else {
            $viewsGrowthText = $totalViews > 0 ? "↑ 100% vs previous 30 days" : "0% vs previous 30 days";
            $viewsGrowthPositive = true;
        }

        // Unique Visitors (Last 30 Days)
        $uniqueVisitors = PageView::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('session_id')
            ->count('session_id');

        $prevUniqueVisitors = PageView::whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->distinct('session_id')
            ->count('session_id');

        if ($prevUniqueVisitors > 0) {
            $visitorsDiff = $uniqueVisitors - $prevUniqueVisitors;
            $visitorsGrowthPercent = round(($visitorsDiff / $prevUniqueVisitors) * 100, 1);
            $visitorsGrowthText = ($visitorsGrowthPercent >= 0 ? "↑ " : "↓ ") . abs($visitorsGrowthPercent) . "% vs previous 30 days";
            $visitorsGrowthPositive = $visitorsGrowthPercent >= 0;
        } else {
            $visitorsGrowthText = $uniqueVisitors > 0 ? "↑ 100% vs previous 30 days" : "0% vs previous 30 days";
            $visitorsGrowthPositive = true;
        }

        // Overview Chart Series (30 daily data points)
        $dailyPageViews = PageView::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $dailyVisitors = PageView::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(DISTINCT session_id) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $chartLabels = [];
        $chartPageViews = [];
        $chartUniqueVisitors = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $chartLabels[] = $date->format('M d');
            $chartPageViews[] = (int) ($dailyPageViews[$dateKey] ?? 0);
            $chartUniqueVisitors[] = (int) ($dailyVisitors[$dateKey] ?? 0);
        }

        // Recent Inquiries
        $recentInquiries = Inquiry::with('property')
            ->latest()
            ->take(5)
            ->get();

        // Top Properties by real views_count
        $topProperties = Property::with(['location', 'category'])
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get();

        // Top Articles by views
        $topArticles = Article::withCount('views')
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
            'viewsGrowthText',
            'viewsGrowthPositive',
            'uniqueVisitors',
            'visitorsGrowthText',
            'visitorsGrowthPositive',
            'chartLabels',
            'chartPageViews',
            'chartUniqueVisitors',
            'recentInquiries',
            'topProperties',
            'topArticles'
        ));
    }
}
