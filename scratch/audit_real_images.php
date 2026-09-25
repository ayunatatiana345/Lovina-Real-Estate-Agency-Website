<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage;

echo "========================================================================================\n";
echo "AUDIT REPORT: REAL UPLOADED PROPERTY GALLERY IMAGES IN DATABASE & STORAGE\n";
echo "========================================================================================\n\n";

$images = PropertyImage::with('property.location')->orderBy('sort_order', 'asc')->get();
echo "Total Image Records Found in DB: " . $images->count() . "\n\n";

$finfo = finfo_open(FILEINFO_MIME_TYPE);

foreach ($images as $img) {
    $prop = $img->property;
    $relPath = $img->image_path;
    $filename = basename($relPath);
    $diskPath = Storage::disk('public')->path($relPath);
    $publicPath = public_path('storage/' . $relPath);

    $existsOnDisk = file_exists($diskPath);
    $existsViaPublic = file_exists($publicPath);

    $mimeType = 'N/A';
    $fileSize = 0;
    $dimensions = 'N/A';
    $isTrueWebp = false;

    if ($existsOnDisk) {
        $mimeType = finfo_file($finfo, $diskPath);
        $fileSize = filesize($diskPath);
        $imgSize = @getimagesize($diskPath);
        if ($imgSize) {
            $dimensions = $imgSize[0] . 'x' . $imgSize[1];
        }
        
        // True WebP format binary check:
        // WebP files begin with RIFF....WEBP in the first 12 bytes
        $header = file_get_contents($diskPath, false, null, 0, 12);
        if (substr($header, 0, 4) === 'RIFF' && substr($header, 8, 4) === 'WEBP') {
            $isTrueWebp = true;
        }
    }

    echo "Image Record ID: #{$img->id}\n";
    echo " - Property: {$prop->name} (ID: {$prop->id}, Slug: {$prop->slug})\n";
    echo " - Location: " . ($prop->location->name ?? 'N/A') . "\n";
    echo " - Stored Filename: {$filename}\n";
    echo " - Database Path: {$relPath}\n";
    echo " - Stored Image Alt: {$img->image_alt}\n";
    echo " - Is Cover Photo: " . ($img->is_cover ? 'YES (★ Main Cover)' : 'NO') . "\n";
    echo " - Sort Order: {$img->sort_order}\n";
    echo " - Physical File Exists: " . ($existsOnDisk ? 'YES' : 'NO') . " (" . round($fileSize / 1024, 2) . " KB)\n";
    echo " - Binary MIME Type: {$mimeType}\n";
    echo " - True WebP Magic Header (RIFF....WEBP): " . ($isTrueWebp ? 'VERIFIED TRUE WEBP' : 'FAILED') . "\n";
    echo " - Dimensions: {$dimensions}\n";
    echo " - SEO Checks:\n";
    echo "    * Lowercase only: " . ($filename === strtolower($filename) ? 'PASS' : 'FAIL') . "\n";
    echo "    * No spaces/underscores: " . (!str_contains($filename, ' ') && !str_contains($filename, '_') ? 'PASS' : 'FAIL') . "\n";
    echo "    * Starts with property slug: " . (str_starts_with($filename, $prop->slug) ? 'PASS' : 'FAIL') . "\n";
    echo "    * Has .webp extension: " . (str_ends_with($filename, '.webp') ? 'PASS' : 'FAIL') . "\n";
    echo "\n";
}

finfo_close($finfo);

// Check Public URL rendering
echo "========================================================================================\n";
echo "PUBLIC PROPERTY DETAIL PAGE CHECK\n";
echo "========================================================================================\n\n";

$publicController = app(\App\Http\Controllers\Public\PropertyController::class);
$view = $publicController->show('four-bedroom-villa-in-sing-sing');
$html = $view->render();

echo "Public View Rendered: " . strlen($html) . " bytes\n";
foreach ($images as $img) {
    $fn = basename($img->image_path);
    $found = str_contains($html, $fn);
    echo " - {$fn} rendered in public page HTML: " . ($found ? 'YES (Found)' : 'NO (Missing)') . "\n";
}

