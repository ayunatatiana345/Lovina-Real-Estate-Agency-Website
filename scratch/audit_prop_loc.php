<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

echo "=== PROPERTY TO LOCATION RELATIONSHIP AUDIT ===\n\n";

// 1. Check all properties for location_id validity
$totalProps = Property::count();
$validLocationIds = Location::pluck('id')->toArray();
$invalidLocProps = Property::whereNotIn('location_id', $validLocationIds)->get();
$nullLocProps = Property::whereNull('location_id')->get();

echo "1. VALIDITY OF LOCATION IN PROPERTIES:\n";
echo "Total Properties: {$totalProps}\n";
echo "Properties with NULL location_id: " . $nullLocProps->count() . "\n";
echo "Properties referencing non-existing location_id: " . $invalidLocProps->count() . "\n";

// 2. Check property counts calculation per location
echo "\n2. LOCATION PROPERTY COUNT COMPARISON:\n";
$locations = Location::withCount(['properties', 'properties as published_properties_count' => function($q) {
    $q->where('status', 'published');
}])->get();

foreach ($locations as $loc) {
    $rawCount = Property::where('location_id', $loc->id)->count();
    $rawPublishedCount = Property::where('location_id', $loc->id)->where('status', 'published')->count();
    $modelAttrCount = $loc->property_count; // Uses getPropertyCountAttribute()
    
    $mismatch = ($rawPublishedCount !== $loc->published_properties_count) || ($rawPublishedCount !== $modelAttrCount);
    
    echo sprintf("Location [%2d] %-18s (Status: %-8s) | Total DB Props: %2d | Published DB Props: %2d | withCount(published): %2d | ModelAttr(published): %2d | Mismatch: %s\n",
        $loc->id,
        $loc->name,
        $loc->status,
        $rawCount,
        $rawPublishedCount,
        $loc->published_properties_count,
        $modelAttrCount,
        ($mismatch ? 'YES (MISMATCH!)' : 'NO')
    );
}

// 3. Check for Duplicate Location Names / Similar Names
echo "\n3. LOCATION DUPLICATE & NAMING CONSISTENCY CHECK:\n";
$locNames = Location::all();
$normalizedNames = [];
foreach ($locNames as $l) {
    $norm = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $l->name)));
    $normalizedNames[$norm][] = "[ID: {$l->id}] '{$l->name}' (Slug: {$l->slug})";
}

$duplicateFound = false;
foreach ($normalizedNames as $norm => $group) {
    if (count($group) > 1) {
        $duplicateFound = true;
        echo "Potential Duplicate Locations found:\n";
        foreach ($group as $item) {
            echo "  - {$item}\n";
        }
    }
}
if (!$duplicateFound) {
    echo "No duplicate location names found.\n";
}

// 4. Check for properties assigned to inactive location
echo "\n4. PROPERTIES ASSIGNED TO INACTIVE LOCATIONS:\n";
$inactiveLocIds = Location::where('status', 'inactive')->pluck('id')->toArray();
$propsInInactiveLocs = Property::whereIn('location_id', $inactiveLocIds)->with('location')->get();

echo "Properties in Inactive Locations count: " . $propsInInactiveLocs->count() . "\n";
foreach ($propsInInactiveLocs as $p) {
    echo "  - Property ID {$p->id}: '{$p->name}' | Status: {$p->status} | Location: '{$p->location->name}' (Location ID {$p->location_id}, Status: {$p->location->status})\n";
}

