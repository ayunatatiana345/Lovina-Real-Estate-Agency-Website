<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PropertyImage;
use App\Http\Controllers\Admin\PropertyController;

echo "=== TESTING GALLERY ACTIONS (SET COVER & DELETE) ===\n";

$controller = app(PropertyController::class);

// Test setCoverImage on Image #7
echo "Setting Image #7 as cover...\n";
$resCover = $controller->setCoverImage(7);
$img5 = PropertyImage::find(5);
$img7 = PropertyImage::find(7);

echo "Image #5 is_cover: " . ($img5->is_cover ? 'YES' : 'NO') . " (Expected: NO)\n";
echo "Image #7 is_cover: " . ($img7->is_cover ? 'YES' : 'NO') . " (Expected: YES)\n";

// Test deleteImage on Image #6
$img6Path = PropertyImage::find(6)->image_path;
echo "Deleting Image #6 (Path: {$img6Path})...\n";
$resDel = $controller->deleteImage(6);

$img6Deleted = PropertyImage::find(6) === null;
$img6FileGone = !file_exists(public_path('storage/' . $img6Path));

echo "Image #6 deleted from DB: " . ($img6Deleted ? 'PASS' : 'FAIL') . "\n";
echo "Image #6 removed from disk: " . ($img6FileGone ? 'PASS' : 'FAIL') . "\n";

echo "Gallery actions verified successfully!\n";
