<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

echo "=======================================================\n";
echo "SAFE REFINEMENTS & CONTENT WORKFLOW VERIFICATION SCRIPT\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($condition, $name) {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $name . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $name . "\n";
        $failCount++;
    }
}

// -----------------------------------------------------------
// 1. TEST CATEGORY DELETION GUARD (Prevent CASCADE)
// -----------------------------------------------------------
echo "--- 1. Testing Category System & Safety Guard ---\n";
$villaCategory = PropertyCategory::where('name', 'Villa')->first();
assertTest($villaCategory !== null, "Villa category exists");

$propCount = $villaCategory->properties()->count();
assertTest($propCount > 0, "Villa category has {$propCount} assigned properties");

// Attempt to delete Villa category via controller
$categoryController = app(\App\Http\Controllers\Admin\PropertyCategoryController::class);
$deleteResponse = $categoryController->destroy($villaCategory->id);
$villaCategoryAfter = PropertyCategory::find($villaCategory->id);

assertTest($villaCategoryAfter !== null, "Villa category was PROTECTED from deletion by backend guard");
assertTest(session('error') !== null, "Session error message was set: " . session('error'));

// Test Toggle Status on Category
$initialStatus = $villaCategory->status;
$categoryController->toggleStatus($villaCategory->id);
$toggledStatus = PropertyCategory::find($villaCategory->id)->status;
assertTest($toggledStatus === ($initialStatus === 'active' ? 'inactive' : 'active'), "Category status toggled from {$initialStatus} to {$toggledStatus}");

// Toggle it back to active
$categoryController->toggleStatus($villaCategory->id);
$restoredStatus = PropertyCategory::find($villaCategory->id)->status;
assertTest($restoredStatus === 'active', "Category status restored back to active");


// -----------------------------------------------------------
// 2. TEST FEATURED PROPERTY LIMIT (Strict Max 6)
// -----------------------------------------------------------
echo "\n--- 2. Testing Featured Properties Limit (Max 6) ---\n";
$currentFeaturedCount = Property::where('is_featured', true)->count();
echo "Current featured properties count: {$currentFeaturedCount}\n";
assertTest($currentFeaturedCount === 6, "Currently exactly 6 properties are featured");

$unfeaturedProp = Property::where('is_featured', false)->where('status', 'published')->first();
assertTest($unfeaturedProp !== null, "Found unfeatured published property (ID: {$unfeaturedProp->id}, Name: {$unfeaturedProp->name})");

// Attempt to feature 7th property via toggleFeatured
$propController = app(\App\Http\Controllers\Admin\PropertyController::class);
$toggleResponse = $propController->toggleFeatured($unfeaturedProp->id);
$unfeaturedAfter = Property::find($unfeaturedProp->id);
$featuredCountAfter = Property::where('is_featured', true)->count();

assertTest($unfeaturedAfter->is_featured === false, "7th property was BLOCKED from being featured");
assertTest($featuredCountAfter === 6, "Featured count remains strictly at 6");
assertTest(session('error') !== null, "Informative error flash message was generated: " . session('error'));

// Test unfeaturing an existing featured property
$existingFeatured = Property::where('is_featured', true)->first();
$featuredId = $existingFeatured->id;
$propController->toggleFeatured($featuredId);
$unfeaturedCheck = Property::find($featuredId);
assertTest($unfeaturedCheck->is_featured === false, "Existing featured property unfeatured successfully (ID: {$featuredId})");
assertTest(Property::where('is_featured', true)->count() === 5, "Featured count decreased to 5");

// Now feature it back to reach 6
$propController->toggleFeatured($featuredId);
assertTest(Property::find($featuredId)->is_featured === true, "Property re-featured successfully back to 6");
assertTest(Property::where('is_featured', true)->count() === 6, "Featured count returned to exactly 6");


// -----------------------------------------------------------
// 3. TEST HOMEPAGE FEATURED SECTION (No Non-Featured Supplement)
// -----------------------------------------------------------
echo "\n--- 3. Testing Homepage Featured Properties Query ---\n";
$homeController = app(\App\Http\Controllers\Public\HomeController::class);
$currencyService = app(\App\Services\CurrencyService::class);
$view = $homeController->index($currencyService);
$viewData = $view->getData();

$homeFeatured = $viewData['featuredProperties'];
assertTest($homeFeatured !== null, "HomeController passed featuredProperties to view");
assertTest($homeFeatured->count() <= 6, "Featured properties count is <= 6 (actual: {$homeFeatured->count()})");

$allAreFeatured = true;
foreach ($homeFeatured as $hp) {
    if (!$hp->is_featured) {
        $allAreFeatured = false;
        break;
    }
}
assertTest($allAreFeatured === true, "ALL properties returned to Homepage Featured Section have is_featured = true (NO non-featured fillers)");


// -----------------------------------------------------------
// 4. TEST PROPERTY SHOW VIEW (No hardcoded room titles)
// -----------------------------------------------------------
echo "\n--- 4. Testing Property Detail Page Gallery Captions ---\n";
$sampleProp = Property::where('status', 'published')->has('images')->first();
$showBladePath = resource_path('views/public/properties/show.blade.php');
$bladeContent = file_get_contents($showBladePath);

assertTest(strpos($bladeContent, '$roomTitles') === false, "Hardcoded \$roomTitles array removed from show.blade.php");
assertTest(strpos($bladeContent, 'Living Room') === false, "Hardcoded 'Living Room' removed from show.blade.php");
assertTest(strpos($bladeContent, 'Exterior View') === false, "Hardcoded 'Exterior View' removed from show.blade.php");
assertTest(strpos($bladeContent, "'Photo ' . \$photoNumber") !== false || strpos($bladeContent, '"Photo " . $photoNumber') !== false, "Dynamic 'Photo ' . \$photoNumber caption implemented in gallery cards");
assertTest(strpos($bladeContent, 'Photo 1 of') !== false, "Dynamic 'Photo 1 of' caption implemented in lightbox default view");


// -----------------------------------------------------------
// 5. TEST LOCATION MAP ORIENTATION IN LOCATIONS INDEX
// -----------------------------------------------------------
echo "\n--- 5. Testing Location Map Orientation in Locations View ---\n";
$locIndexBlade = resource_path('views/public/locations/index.blade.php');
$locBladeContent = file_get_contents($locIndexBlade);

assertTest(strpos($locBladeContent, 'Visual map orientation of North Bali service areas') !== false, "Orientation explanation note present in locations index blade");
assertTest(strpos($locBladeContent, "route('locations.show', \$loc->slug)") !== false, "Map pins link dynamically to route('locations.show', \$loc->slug)");
assertTest(strpos($locBladeContent, "'gerokgak'") !== false, "Map coords include westernmost point 'gerokgak'");
assertTest(strpos($locBladeContent, "'bondalem'") !== false, "Map coords include easternmost point 'bondalem'");
assertTest(strpos($locBladeContent, "'sambangan'") !== false, "Map coords include hill region 'sambangan'");


// -----------------------------------------------------------
// 6. TEST ADMIN PROPERTIES INDEX VIEW (No category delete, edit modal present)
// -----------------------------------------------------------
echo "\n--- 6. Testing Admin Properties & Categories Index View ---\n";
$adminPropBlade = resource_path('views/admin/properties/index.blade.php');
$adminContent = file_get_contents($adminPropBlade);

assertTest(strpos($adminContent, "route('admin.categories.destroy'") === false, "Destructive Category Delete button removed from Category table");
assertTest(strpos($adminContent, "openEditCategoryModal") !== false, "Edit Category Modal trigger present in Category table");
assertTest(strpos($adminContent, "admin.categories.toggle-status") !== false, "Category toggle status button present in Category table");
assertTest(strpos($adminContent, "admin.properties.toggle-featured") !== false, "Property quick toggle featured button present in Property table");

echo "\n=======================================================\n";
echo "TEST RESULTS: {$passCount} PASSED, {$failCount} FAILED\n";
echo "=======================================================\n";

if ($failCount === 0) {
    echo "ALL SAFE REFINEMENT TESTS PASSED PERFECTLY!\n";
} else {
    exit(1);
}
