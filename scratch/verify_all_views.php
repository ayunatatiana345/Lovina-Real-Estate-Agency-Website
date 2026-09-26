<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Location;
use App\Models\Property;
use App\Models\CompanySetting;
use App\Services\CurrencyService;
use Illuminate\Support\ViewErrorBag;

echo "========================================================\n";
echo "VERIFYING BLADE VIEWS RENDERING\n";
echo "========================================================\n";

view()->share('errors', new ViewErrorBag);

$currencyService = app(CurrencyService::class);
$settings = CompanySetting::getSettings();
$locations = Location::withCount(['properties'])->get();

// 1. Render Admin Locations Index
echo "\n1. Rendering admin.locations.index...\n";
$view1 = view('admin.locations.index', compact('locations', 'settings'))->render();
echo "  -> Rendered length: " . strlen($view1) . " bytes\n";
echo "  [PASS] admin.locations.index rendered without errors!\n";

// 2. Render Public Locations Index
echo "\n2. Rendering public.locations.index...\n";
$categories = \App\Models\PropertyCategory::where('status', 'active')->get();
$priceRangeOptions = $currencyService->getPriceRangeOptions();
$allLocations = Location::where('status', 'active')->withCount(['properties' => function ($q) {
    $q->where('status', 'published');
}])->get();
$totalLocations = Location::where('status', 'active')->count();
$totalProperties = Property::where('status', 'published')->count();
$locationsActive = Location::where('status', 'active')->withCount(['properties' => function ($q) {
    $q->where('status', 'published');
}])->get();

$view2 = view('public.locations.index', [
    'locations' => $locationsActive,
    'allLocations' => $allLocations,
    'totalLocations' => $totalLocations,
    'totalProperties' => $totalProperties,
    'settings' => $settings,
    'categories' => $categories,
    'priceRangeOptions' => $priceRangeOptions,
])->render();
echo "  -> Rendered length: " . strlen($view2) . " bytes\n";
echo "  [PASS] public.locations.index rendered without errors!\n";

// 3. Render Public Locations Show
echo "\n3. Rendering public.locations.show...\n";
$firstLoc = Location::where('status', 'active')->first();
$properties = Property::where('location_id', $firstLoc->id)->where('status', 'published')->paginate(12);
$otherLocations = Location::where('status', 'active')->where('id', '!=', $firstLoc->id)->take(5)->get();

$view3 = view('public.locations.show', [
    'location' => $firstLoc,
    'properties' => $properties,
    'otherLocations' => $otherLocations,
    'categories' => $categories,
    'settings' => $settings,
])->render();
echo "  -> Rendered length: " . strlen($view3) . " bytes\n";
echo "  [PASS] public.locations.show rendered without errors!\n";

echo "\n========================================================\n";
echo "ALL VIEWS RENDERED CLEANLY & SUCCESSFULLY!\n";
echo "========================================================\n";
