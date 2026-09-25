<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\PropertyImage;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Public\PropertyController as PublicPropertyController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

echo "=== STARTING FULL END-TO-END PROPERTY UPLOAD TEST ===\n\n";

$propA = Property::with(['images', 'location'])->findOrFail(148);
echo "Property A: ID {$propA->id} - {$propA->name} (slug: {$propA->slug})\n";
$initialCountA = PropertyImage::where('property_id', $propA->id)->count();
echo "Initial Image Count for Property A: {$initialCountA}\n";

$propB = Property::findOrFail(1);
echo "Property B (Control): ID {$propB->id} - {$propB->name}\n";
$initialCountB = PropertyImage::where('property_id', $propB->id)->count();

// Create 3 temporary sample images with random filenames
$tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'e2e_upload_test_' . time();
mkdir($tempDir, 0777, true);

function makeImg($file, $w, $h, $r, $g, $b, $isPng = false) {
    $im = imagecreatetruecolor($w, $h);
    $col = imagecolorallocate($im, $r, $g, $b);
    imagefilledrectangle($im, 0, 0, $w, $h, $col);
    $white = imagecolorallocate($im, 255, 255, 255);
    imagestring($im, 4, 10, 10, "E2E Sample Photo", $white);
    if ($isPng) {
        imagepng($im, $file);
    } else {
        imagejpeg($im, $file, 85);
    }
    imagedestroy($im);
}

$path1 = $tempDir . '/WhatsApp Image 2026-09-19 at 14.20.55.jpeg';
$path2 = $tempDir . '/IMG_0091.JPG';
$path3 = $tempDir . '/DSC_8821.PNG';

makeImg($path1, 1400, 933, 40, 90, 160, false);
makeImg($path2, 1200, 800, 160, 90, 40, false);
makeImg($path3, 1000, 750, 40, 160, 90, true);

$uploadedFile1 = new UploadedFile($path1, 'WhatsApp Image 2026-09-19 at 14.20.55.jpeg', 'image/jpeg', null, true);
$uploadedFile2 = new UploadedFile($path2, 'IMG_0091.JPG', 'image/jpeg', null, true);
$uploadedFile3 = new UploadedFile($path3, 'DSC_8821.PNG', 'image/png', null, true);

echo "\nSubmitting update request to Admin PropertyController...\n";

$reqData = [
    'name' => $propA->name,
    'slug' => $propA->slug,
    'category_id' => $propA->category_id,
    'category_ids' => $propA->categories->pluck('id')->toArray(),
    'location_id' => $propA->location_id,
    'price' => $propA->price,
    'ownership_type' => $propA->ownership_type ?: 'Freehold',
    'status' => $propA->status ?: 'published',
    'description' => $propA->description,
    'new_cover_index' => 0, // Designate first new upload as cover
];

$request = Request::create(
    "/admin/properties/{$propA->id}",
    'PUT',
    $reqData,
    [], // cookies
    ['images' => [$uploadedFile1, $uploadedFile2, $uploadedFile3]] // files
);

$session = $app->make('session')->driver();
$request->setLaravelSession($session);

$adminController = app(AdminPropertyController::class);
$response = $adminController->update($request, $propA->id);

echo "Controller Response: Redirect to " . $response->getTargetUrl() . "\n";
if ($session->get('success')) {
    echo "Session Success Flash: " . $session->get('success') . "\n";
}
if ($session->get('errors')) {
    echo "Session Errors: " . json_encode($session->get('errors')->all()) . "\n";
}

echo "\n=== DATABASE VERIFICATION ===\n";
$newImagesA = PropertyImage::where('property_id', $propA->id)->orderBy('sort_order', 'asc')->get();
echo "New Image Count for Property A: " . $newImagesA->count() . " (Expected: 3)\n";

$paths = [];
$coverCount = 0;

foreach ($newImagesA as $i => $img) {
    $num = $i + 1;
    echo "Image #{$num}:\n";
    echo " - ID: {$img->id}\n";
    echo " - Path: {$img->image_path}\n";
    echo " - Alt Text: {$img->image_alt}\n";
    echo " - Is Cover: " . ($img->is_cover ? 'YES' : 'NO') . "\n";
    echo " - Sort Order: {$img->sort_order}\n";

    $paths[] = $img->image_path;
    if ($img->is_cover) $coverCount++;

    // Assertions
    $isWebp = str_ends_with($img->image_path, '.webp');
    $startsSlug = str_starts_with($img->image_path, 'properties/' . $propA->slug . '/');
    $hasAlt = !empty($img->image_alt) && str_contains($img->image_alt, 'Sing-Sing');
    $diskExists = file_exists(public_path('storage/' . $img->image_path));

    echo "   [Check] Ends with .webp? " . ($isWebp ? 'PASS' : 'FAIL') . "\n";
    echo "   [Check] In properties/{slug}/? " . ($startsSlug ? 'PASS' : 'FAIL') . "\n";
    echo "   [Check] Alt contains property context? " . ($hasAlt ? 'PASS' : 'FAIL') . "\n";
    echo "   [Check] Exists in public storage? " . ($diskExists ? 'PASS' : 'FAIL') . "\n";
}

$uniquePaths = count(array_unique($paths)) === count($paths);
echo "\nAll 3 filenames unique? " . ($uniquePaths ? 'PASS' : 'FAIL') . "\n";
echo "Exactly 1 cover photo? " . ($coverCount === 1 ? 'PASS' : 'FAIL') . "\n";

echo "\n=== CONTROL PROPERTY CHECK ===\n";
$afterCountB = PropertyImage::where('property_id', $propB->id)->count();
echo "Property B images before: {$initialCountB}, after: {$afterCountB} (PASS: " . ($initialCountB === $afterCountB ? 'YES' : 'NO') . ")\n";

echo "\n=== PUBLIC DETAIL PAGE RENDERING CHECK ===\n";
$publicController = app(PublicPropertyController::class);
$publicView = $publicController->show($propA->slug);
$html = $publicView->render();

echo "Public View Rendered: " . strlen($html) . " bytes\n";

$allFoundInHtml = true;
foreach ($newImagesA as $img) {
    $filename = basename($img->image_path);
    $inHtml = str_contains($html, $filename);
    echo " - Processed image '{$filename}' found in HTML? " . ($inHtml ? 'PASS' : 'FAIL') . "\n";
    if (!$inHtml) $allFoundInHtml = false;
}

$altInHtml = str_contains($html, 'Four Bedroom Villa in Sing-Sing, North Bali');
echo " - Expected Alt text found in HTML? " . ($altInHtml ? 'PASS' : 'FAIL') . "\n";

echo "\n=== ALL CHECKS FINISHED ===\n";
