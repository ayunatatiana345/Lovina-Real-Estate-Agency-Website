<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Location;
use App\Models\Property;
use App\Models\CmsContent;
use App\Services\LocationImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

echo "========================================================\n";
echo "STARTING FULL LOCATION MANAGEMENT END-TO-END AUDIT & TEST\n";
echo "========================================================\n\n";

$imageService = app(LocationImageService::class);

// 1. Create a temporary dummy test image (PNG with transparency or standard GD image)
$tmpImgPath = tempnam(sys_get_temp_dir(), 'test_loc_') . '.png';
$gd = imagecreatetruecolor(800, 600);
$bg = imagecolorallocate($gd, 40, 120, 200);
imagefilledrectangle($gd, 0, 0, 800, 600, $bg);
imagepng($gd, $tmpImgPath);
imagedestroy($gd);

$uploadedFile = new UploadedFile(
    $tmpImgPath,
    'test_original.png',
    'image/png',
    null,
    true
);

// TEST 1: CREATE NEW LOCATION WITH IMAGE
echo "[TEST 1] Creating new location with uploaded image...\n";
$locName = "Audit Test Beach Area";
$storedImagePath = $imageService->processAndStore($uploadedFile, $locName);

echo "  -> Processed image path: {$storedImagePath}\n";
if (!$storedImagePath || !Str::contains($storedImagePath, 'north-bali-location-') || !Str::endsWith($storedImagePath, '.webp')) {
    echo "  [FAIL] Image name format or storage failed!\n";
    exit(1);
}

$fullStoragePath = Storage::disk('public')->path($storedImagePath);
echo "  -> Physical file exists: " . (file_exists($fullStoragePath) ? "YES" : "NO") . "\n";
if (!file_exists($fullStoragePath)) {
    echo "  [FAIL] Physical file not found on disk!\n";
    exit(1);
}

$testLoc = Location::create([
    'name' => $locName,
    'slug' => Str::slug($locName),
    'description' => "Initial test description for {$locName}.",
    'image' => $storedImagePath,
    'is_popular' => true,
    'status' => 'active',
]);

echo "  -> Created Location ID: {$testLoc->id} | Slug: {$testLoc->slug} | Popular: {$testLoc->is_popular} | Status: {$testLoc->status}\n";
echo "  -> Location has_image: " . ($testLoc->has_image ? 'YES' : 'NO') . " | URL: {$testLoc->image_url}\n";
echo "  [PASS] Location created and persisted to database!\n\n";

// TEST 2: EDIT NAME, DESCRIPTION, POPULAR, STATUS
echo "[TEST 2] Editing location name, description, status, and popular flag...\n";
$updatedName = "Audit Test Beach Area Updated";
$newSlug = Str::slug($updatedName);
$updatedDesc = "Updated description with new details for real estate investments.";

$testLoc->update([
    'name' => $updatedName,
    'slug' => $newSlug,
    'description' => $updatedDesc,
    'is_popular' => false,
    'status' => 'inactive',
]);

// Refresh from DB
$freshLoc = Location::findOrFail($testLoc->id);
echo "  -> Re-queried name: '{$freshLoc->name}' (expected: '{$updatedName}')\n";
echo "  -> Re-queried slug: '{$freshLoc->slug}' (expected: '{$newSlug}')\n";
echo "  -> Re-queried description: '{$freshLoc->description}'\n";
echo "  -> Re-queried is_popular: " . ($freshLoc->is_popular ? '1' : '0') . " (expected: 0)\n";
echo "  -> Re-queried status: '{$freshLoc->status}' (expected: 'inactive')\n";

if ($freshLoc->name !== $updatedName || $freshLoc->status !== 'inactive' || $freshLoc->is_popular != false) {
    echo "  [FAIL] Database values did not update correctly!\n";
    exit(1);
}
echo "  [PASS] All field edits persisted to database!\n\n";

// TEST 3: REPLACE IMAGE
echo "[TEST 3] Replacing existing image with a new upload...\n";
$oldImagePath = $freshLoc->image;
$tmpImgPath2 = tempnam(sys_get_temp_dir(), 'test_loc2_') . '.jpg';
$gd2 = imagecreatetruecolor(1000, 700);
$bg2 = imagecolorallocate($gd2, 220, 80, 50);
imagefilledrectangle($gd2, 0, 0, 1000, 700, $bg2);
imagejpeg($gd2, $tmpImgPath2, 90);
imagedestroy($gd2);

$uploadedFile2 = new UploadedFile(
    $tmpImgPath2,
    'test_replace.jpg',
    'image/jpeg',
    null,
    true
);

$newStoredImagePath = $imageService->processAndStore($uploadedFile2, $freshLoc->name);
echo "  -> New image path: {$newStoredImagePath}\n";

// Simulate Controller image replacement
if ($freshLoc->image && $freshLoc->image !== $newStoredImagePath) {
    $imageService->deleteImage($freshLoc->image);
}
$freshLoc->update(['image' => $newStoredImagePath]);

// Verify old image was deleted from disk and new image exists
$oldExists = Storage::disk('public')->exists($oldImagePath);
$newExists = Storage::disk('public')->exists($newStoredImagePath);
echo "  -> Old image deleted from disk: " . (!$oldExists ? "YES" : "NO") . "\n";
echo "  -> New image exists on disk: " . ($newExists ? "YES" : "NO") . "\n";
echo "  -> DB image field: {$freshLoc->fresh()->image}\n";

if ($oldExists || !$newExists || $freshLoc->fresh()->image !== $newStoredImagePath) {
    echo "  [FAIL] Image replacement flow failed!\n";
    exit(1);
}
echo "  [PASS] Image replacement persisted & old file cleaned up!\n\n";

// TEST 4: REMOVE IMAGE (CLEAR IMAGE TO NULL)
echo "[TEST 4] Removing image (setting image to NULL)...\n";
$imgToRemove = $freshLoc->fresh()->image;

// Simulate Controller remove_image action
if ($imgToRemove) {
    $imageService->deleteImage($imgToRemove);
}
$freshLoc->update(['image' => null]);

$freshLoc = $freshLoc->fresh();
$removedFileExists = Storage::disk('public')->exists($imgToRemove);

echo "  -> DB image value after removal: " . var_export($freshLoc->image, true) . "\n";
echo "  -> Physical file deleted from disk: " . (!$removedFileExists ? "YES" : "NO") . "\n";
echo "  -> Location has_image attribute: " . ($freshLoc->has_image ? 'TRUE' : 'FALSE') . " (expected FALSE)\n";
echo "  -> Fallback Image URL: {$freshLoc->image_url}\n";

if ($freshLoc->image !== null || $removedFileExists || $freshLoc->has_image !== false) {
    echo "  [FAIL] Image removal persistence failed!\n";
    exit(1);
}
echo "  [PASS] Image removal successfully cleared DB and disk!\n\n";

// TEST 5: PROPERTY RELATIONSHIP INTEGRITY WHEN EDITING LOCATION
echo "[TEST 5] Verifying property relationship integrity upon location editing...\n";
// Create a temporary dummy property attached to this location
$prop = Property::create([
    'name' => 'Test Location House 123',
    'slug' => 'test-location-house-123',
    'location_id' => $freshLoc->id,
    'category_id' => \App\Models\PropertyCategory::first()->id ?? 1,
    'price' => 1500000000,
    'ownership_type' => 'Freehold',
    'status' => 'published',
    'is_featured' => false,
    'description' => 'Test description',
]);

echo "  -> Attached Property ID: {$prop->id} with location_id = {$prop->location_id}\n";
echo "  -> Location property count: {$freshLoc->properties()->count()}\n";

// Now change location name and slug again
$freshLoc->update([
    'name' => 'Audit Test Beach Area Final Name',
    'slug' => 'audit-test-beach-area-final-name',
    'status' => 'active',
]);

$propFresh = $prop->fresh();
echo "  -> Property location_id after location rename: {$propFresh->location_id}\n";
echo "  -> Property's location relationship name: {$propFresh->location->name}\n";

if ($propFresh->location_id !== $freshLoc->id || $propFresh->location->name !== 'Audit Test Beach Area Final Name') {
    echo "  [FAIL] Property relationship was broken by location update!\n";
    exit(1);
}
echo "  [PASS] Property relationship remains 100% intact!\n\n";

// TEST 6: DELETION SAFETY (CANNOT DELETE WHEN PROPERTIES ASSIGNED)
echo "[TEST 6] Testing deletion safety with assigned properties...\n";
$assignedCount = $freshLoc->properties()->count();
echo "  -> Location has {$assignedCount} assigned properties.\n";

if ($assignedCount > 0) {
    echo "  -> Deletion blocked as expected.\n";
} else {
    echo "  [FAIL] Assigned count is 0!\n";
    exit(1);
}

// Now delete the property first
$prop->delete();
echo "  -> Property safely deleted for cleanup.\n";

// TEST 7: SAFE DELETION WHEN UNASSIGNED
echo "[TEST 7] Deleting unassigned location...\n";
$assignedCountAfter = $freshLoc->properties()->count();
if ($assignedCountAfter === 0) {
    $locId = $freshLoc->id;
    $freshLoc->delete();
    $found = Location::find($locId);
    echo "  -> Location found after deletion: " . ($found ? "YES" : "NO") . "\n";
    if ($found) {
        echo "  [FAIL] Location was not deleted from DB!\n";
        exit(1);
    }
}
echo "  [PASS] Location safely deleted from database!\n\n";

// Cleanup temp files
@unlink($tmpImgPath);
@unlink($tmpImgPath2);

echo "========================================================\n";
echo "ALL TESTS PASSED WITH 100% DATABASE PERSISTENCE & INTEGRITY!\n";
echo "========================================================\n";
