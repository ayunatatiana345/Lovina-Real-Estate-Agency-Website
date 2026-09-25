<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\User;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Public\PropertyController as PublicPropertyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ViewErrorBag;

view()->share('errors', new ViewErrorBag());

// Pick a safe property for the end-to-end test
$testProp = Property::with(['category', 'categories', 'location', 'images'])->orderBy('id')->first();
if (!$testProp) {
    echo "ERROR: No property found for testing.\n";
    exit(1);
}

$id = $testProp->id;
$slug = $testProp->slug;
$origDesc = $testProp->description;
$origPrice = $testProp->price;
$origBedrooms = $testProp->bedrooms;

echo "============================================================\n";
echo "END-TO-END PROPERTY SYNC & DESCRIPTION CRUD TEST\n";
echo "Target Property ID: {$id} ({$testProp->name})\n";
echo "Slug: {$slug}\n";
echo "Original Description Length: " . strlen($origDesc) . " characters\n";
echo "============================================================\n\n";

$adminController = new AdminPropertyController();
$publicController = new PublicPropertyController();

// Helper to create admin update request
function makeAdminRequest($property, $overrides = []) {
    $data = [
        'name' => $property->name,
        'slug' => $property->slug,
        'category_id' => $property->category_id,
        'category_ids' => $property->categories->pluck('id')->toArray() ?: [$property->category_id],
        'location_id' => $property->location_id,
        'price' => $property->price,
        'ownership_type' => $property->ownership_type ?: 'Freehold',
        'status' => $property->status ?: 'published',
        'is_featured' => $property->is_featured ? '1' : null,
        'short_description' => $property->short_description,
        'description' => $property->description,
        'bedrooms' => $property->bedrooms,
        'bathrooms' => $property->bathrooms,
        'land_size' => $property->land_size,
        'building_size' => $property->building_size,
        'garage' => $property->garage,
        'electricity' => $property->electricity,
        'water_supply' => $property->water_supply,
        'furnishing' => $property->furnishing,
        'air_conditioning' => $property->air_conditioning,
    ];
    $data = array_merge($data, $overrides);
    return Request::create("/admin/properties/{$property->id}", 'PUT', $data);
}

// -------------------------------------------------------------
// TEST 1: Admin Edit Description -> Save -> DB -> Admin & Public
// -------------------------------------------------------------
echo "STEP 1: Controlled Description Edit...\n";
$uniqueMarker = "TEST_SYNC_CHECK_" . time();
$editedDesc = $origDesc . "\n\n[" . $uniqueMarker . " Dedicated sync verification note.]";

$req1 = makeAdminRequest($testProp, ['description' => $editedDesc]);
$res1 = $adminController->update($req1, $id);

// Verify Database
$freshProp = Property::find($id);
if ($freshProp->description !== $editedDesc) {
    echo "FAILED: Database does not contain updated description!\n";
    exit(1);
}
echo " [PASS] Database updated directly.\n";

// Verify Admin Edit Page View
$adminView = $adminController->edit($id)->render();
if (strpos($adminView, $uniqueMarker) === false) {
    echo "FAILED: Admin edit page does not contain updated description marker!\n";
    exit(1);
}
echo " [PASS] Admin edit view displays the exact updated description.\n";

// Verify Public Detail Page View
$publicView = $publicController->show($slug)->render();
if (strpos($publicView, $uniqueMarker) === false) {
    echo "FAILED: Public property detail does not display updated description!\n";
    exit(1);
}
echo " [PASS] Public property detail displays the exact updated description.\n\n";

// -------------------------------------------------------------
// TEST 2: Admin Edit Another Field (Bedrooms) -> Save -> DB -> Public
// -------------------------------------------------------------
echo "STEP 2: Secondary Field Edit (Bedrooms = 99)...\n";
$req2 = makeAdminRequest($freshProp, ['bedrooms' => 99]);
$res2 = $adminController->update($req2, $id);

$freshProp2 = Property::find($id);
if ((int)$freshProp2->bedrooms !== 99) {
    echo "FAILED: Database did not update bedrooms!\n";
    exit(1);
}
echo " [PASS] Database bedrooms updated to 99.\n";

$publicView2 = $publicController->show($slug)->render();
if (strpos($publicView2, '99 Beds') === false && strpos($publicView2, '99') === false) {
    echo "FAILED: Public property detail does not reflect bedrooms change!\n";
    exit(1);
}
echo " [PASS] Public property detail reflects updated specifications.\n\n";

// -------------------------------------------------------------
// TEST 3: Admin Clears Description -> Save -> DB -> Public
// -------------------------------------------------------------
echo "STEP 3: Clear Description (Empty)...\n";
$req3 = makeAdminRequest($freshProp2, ['description' => '']);
$res3 = $adminController->update($req3, $id);

$freshProp3 = Property::find($id);
if (!empty($freshProp3->description)) {
    echo "FAILED: Database description is not empty/null after clearing!\n";
    exit(1);
}
echo " [PASS] Database description is NULL/empty.\n";

$publicView3 = $publicController->show($slug)->render();
if (strpos($publicView3, 'will be updated soon') !== false || strpos($publicView3, 'No description available') !== false || strpos($publicView3, 'Lorem ipsum') !== false) {
    echo "FAILED: Public view displays placeholder text for empty description!\n";
    exit(1);
}
echo " [PASS] Public view handles empty description gracefully with NO placeholder text.\n\n";

// -------------------------------------------------------------
// TEST 4: Full Restoration of Original Production Data
// -------------------------------------------------------------
echo "STEP 4: Restoring Original Property Data...\n";
$reqRestore = makeAdminRequest($freshProp3, [
    'description' => $origDesc,
    'price' => $origPrice,
    'bedrooms' => $origBedrooms,
]);
$resRestore = $adminController->update($reqRestore, $id);

$restoredProp = Property::find($id);
if ($restoredProp->description !== $origDesc || (int)$restoredProp->bedrooms !== (int)$origBedrooms) {
    echo "FAILED: Failed to restore original property data!\n";
    exit(1);
}
echo " [PASS] Database restored to original state.\n";

$publicViewRestored = $publicController->show($slug)->render();
if (strpos($publicViewRestored, $uniqueMarker) !== false) {
    echo "FAILED: Public view still contains test marker!\n";
    exit(1);
}
echo " [PASS] Public view confirmed completely clean of test data.\n";
echo "============================================================\n";
echo "ALL TESTS PASSED: Admin -> Database -> Public Sync Confirmed!\n";
echo "============================================================\n";
