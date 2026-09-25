<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\Location;
use App\Models\PropertyCategory;
use App\Models\Inquiry;
use App\Models\Article;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

echo "=== COMPREHENSIVE VERIFICATION SUITE ===\n\n";

// 1. PROPERTY SEARCH VERIFICATION
echo "--- 1. PROPERTY SEARCH & FILTERS ---\n";
$req = Request::create('/properties', 'GET', ['keyword' => 'Beach']);
$query = Property::with(['category', 'categories', 'location', 'images'])->where('status', 'published');
$keyword = 'Beach';
$query->where(function ($q) use ($keyword) {
    $q->where('name', 'like', "%{$keyword}%")
      ->orWhere('description', 'like', "%{$keyword}%")
      ->orWhereHas('location', function ($lq) use ($keyword) {
          $lq->where('name', 'like', "%{$keyword}%");
      });
});
$results = $query->get();
echo "Keyword search 'Beach' returned " . $results->count() . " properties.\n";

// Category filter
$catReq = Request::create('/properties', 'GET', ['type' => 'villa']);
$villaQuery = Property::where('status', 'published')->whereHas('category', function($q) { $q->where('slug', 'villa'); })->get();
echo "Category filter 'villa' returned " . $villaQuery->count() . " properties.\n";

// Location filter
$locQuery = Property::where('status', 'published')->whereHas('location', function($q) { $q->where('slug', 'dencarik'); })->get();
echo "Location filter 'dencarik' returned " . $locQuery->count() . " properties (includes Prop ID 14: " . ($locQuery->contains('id', 14) ? 'YES' : 'NO') . ").\n";

// Multi-filter
$multiQuery = Property::where('status', 'published')
    ->whereHas('location', function($q) { $q->where('slug', 'dencarik'); })
    ->whereHas('category', function($q) { $q->where('slug', 'land'); })
    ->get();
echo "Multi-filter (Dencarik + Land) returned " . $multiQuery->count() . " properties.\n";

// 2. CURRENCY CONVERSION VERIFICATION
echo "\n--- 2. CURRENCY CONVERSION ---\n";
$currService = app(CurrencyService::class);
$rate = $currService->getUsdToIdrRate();
echo "USD to IDR Rate: " . number_format($rate, 2) . "\n";
$sampleProp = Property::whereNotNull('price')->first();
echo "Sample Prop: '{$sampleProp->name}' | Base IDR Price: " . number_format($sampleProp->price, 0) . " | Display Price (formatted): {$sampleProp->formatted_price}\n";
$currService->setUserCurrency('USD');
echo "After switching to USD: Display Price: {$sampleProp->formatted_price}\n";
$currService->setUserCurrency('IDR');

// 3. LOCATIONS VERIFICATION
echo "\n--- 3. LOCATIONS & RELATIONSHIPS ---\n";
$activeLocations = Location::where('status', 'active')->withCount(['properties' => function($q) {
    $q->where('status', 'published');
}])->get();
echo "Total Active Locations: " . $activeLocations->count() . "\n";
$popularLocations = $activeLocations->where('is_popular', true);
echo "Popular Active Locations: " . $popularLocations->pluck('name')->implode(', ') . "\n";

$dencarikLoc = Location::where('slug', 'dencarik')->withCount(['properties' => function($q) { $q->where('status', 'published'); }])->first();
echo "Dencarik Location properties_count: {$dencarikLoc->properties_count}\n";

// Check inactive locations count of published properties
$inactiveLocs = Location::where('status', 'inactive')->withCount('properties')->get();
echo "Inactive Locations with published properties: " . $inactiveLocs->sum('properties_count') . "\n";

// 4. INQUIRIES VERIFICATION
echo "\n--- 4. INQUIRIES ---\n";
$inquiries = Inquiry::with('property')->get();
echo "Total Inquiries: " . $inquiries->count() . "\n";
echo "Unreplied (new + in_progress): " . $inquiries->whereIn('status', ['new', 'in_progress'])->count() . "\n";
echo "Replied (responded): " . $inquiries->where('status', 'responded')->count() . "\n";
echo "Closed: " . $inquiries->where('status', 'closed')->count() . "\n";

// 5. ARTICLES VERIFICATION
echo "\n--- 5. TANIA'S 3 ARTICLES ---\n";
$taniaSlugs = [
    'how-to-choose-the-right-property-in-bali',
    'things-to-consider-before-investing-in-bali-property',
    'top-areas-in-north-bali-for-villa-investment',
];
foreach ($taniaSlugs as $slug) {
    $art = Article::where('slug', $slug)->first();
    if ($art) {
        $fileOk = file_exists(public_path($art->featured_image)) ? 'EXISTS_ON_DISK' : 'MISSING_FILE';
        echo "- '{$art->title}' | Category: {$art->category} | Status: {$art->status} | Image: {$art->featured_image} ({$fileOk}) | Image URL: {$art->image_url}\n";
    } else {
        echo "- ERROR: Article with slug '{$slug}' missing!\n";
    }
}

echo "\nVerification complete!\n";
