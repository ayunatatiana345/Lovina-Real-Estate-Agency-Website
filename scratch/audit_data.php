<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\Location;
use App\Models\PropertyCategory;
use App\Models\Inquiry;
use App\Models\PropertyImage;

echo "========================================\n";
echo "SUMMARY OF AUDIT\n";
echo "========================================\n";

$locations = Location::withCount('properties')->get();
$categories = PropertyCategory::withCount('properties')->get();
$inquiries = Inquiry::with('property')->get();
$properties = Property::with(['category', 'categories', 'location', 'images'])->get();

echo "Locations total: " . $locations->count() . " (Active: " . $locations->where('status', 'active')->count() . ", Inactive: " . $locations->where('status', 'inactive')->count() . ", Popular: " . $locations->where('is_popular', true)->count() . ")\n";
echo "Categories total: " . $categories->count() . " (Active: " . $categories->where('status', 'active')->count() . ", Inactive: " . $categories->where('status', 'inactive')->count() . ")\n";
echo "Inquiries total: " . $inquiries->count() . "\n";
echo "Properties total: " . $properties->count() . " (Published: " . $properties->where('status', 'published')->count() . ", Draft: " . $properties->where('status', 'draft')->count() . ")\n\n";

echo "--- PROPERTY SPECIFIC FINDINGS ---\n";
// 1. Empty description check
$emptyDesc = $properties->filter(function($p) { return empty(trim($p->description ?? '')); });
echo "1. Properties with EMPTY descriptions: " . $emptyDesc->count() . " (IDs: " . $emptyDesc->pluck('id')->implode(', ') . ")\n";
foreach ($emptyDesc as $p) {
    echo "   - ID {$p->id}: '{$p->name}' | Status: {$p->status}\n";
}

// 2. NULL price check
$nullPrice = $properties->whereNull('price');
echo "2. Properties with NULL price (Price on Request): " . $nullPrice->count() . " (IDs: " . $nullPrice->pluck('id')->implode(', ') . ")\n";

// 3. No images check
$noImages = $properties->filter(function($p) { return $p->images->count() === 0; });
echo "3. Properties with NO images in DB: " . $noImages->count() . " out of " . $properties->count() . "\n";

// 4. Broken relationships check
$brokenLoc = $properties->whereNull('location');
$brokenCat = $properties->whereNull('category');
echo "4. Properties with BROKEN Location FK: " . $brokenLoc->count() . "\n";
echo "   Properties with BROKEN Category FK: " . $brokenCat->count() . "\n";

// 5. Inactive location check
$inactiveLocProps = $properties->filter(function($p) { return $p->location && $p->location->status === 'inactive'; });
echo "5. Published properties assigned to INACTIVE Location: " . $inactiveLocProps->count() . "\n";
foreach ($inactiveLocProps as $p) {
    echo "   - ID {$p->id}: '{$p->name}' | Location: '{$p->location->name}' (ID {$p->location_id}, Status: {$p->location->status}) | Status: {$p->status}\n";
}

// 6. Draft properties check
$draftProps = $properties->where('status', 'draft');
echo "6. Draft properties in DB: " . $draftProps->count() . "\n";
foreach ($draftProps as $p) {
    echo "   - ID {$p->id}: '{$p->name}' | Status: {$p->status}\n";
}

// 7. Duplicate property check
$nameCounts = Property::select('name')->get()->groupBy('name');
$duplicates = $nameCounts->filter(function($group) { return $group->count() > 1; });
echo "7. Duplicate properties count: " . $duplicates->count() . "\n";

// 8. Test/dummy records check
$testProps = Property::where('name', 'like', '%test%')->get();
echo "8. Test property records count: " . $testProps->count() . "\n";

// 9. Categories check
echo "\n--- CATEGORIES BREAKDOWN ---\n";
foreach ($categories as $c) {
    echo "  - ID {$c->id}: {$c->name} (Slug: {$c->slug}) | Status: {$c->status} | Properties count: {$c->properties_count}\n";
}

// 10. Inactive locations check
echo "\n--- INACTIVE LOCATIONS BREAKDOWN ---\n";
foreach ($locations->where('status', 'inactive') as $l) {
    echo "  - ID {$l->id}: {$l->name} (Slug: {$l->slug}) | Status: {$l->status} | Props count: {$l->properties_count}\n";
}

