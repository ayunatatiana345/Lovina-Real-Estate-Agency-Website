<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\PropertyImage;
use App\Services\PropertyImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

echo "=== STARTING AUTOMATIC IMAGE PROCESSING VERIFICATION ===\n\n";

$property = Property::with('location')->findOrFail(148);
echo "Target Property: ID {$property->id} - {$property->name} (slug: {$property->slug})\n";
echo "Location: " . ($property->location->name ?? 'N/A') . "\n\n";

$tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'lovina_test_imgs_' . time();
if (!file_exists($tempDir)) {
    mkdir($tempDir, 0777, true);
}

// 1. Helper to generate real test images
function createTestJpeg($path, $w, $h, $r, $g, $b) {
    $im = imagecreatetruecolor($w, $h);
    $color = imagecolorallocate($im, $r, $g, $b);
    imagefilledrectangle($im, 0, 0, $w, $h, $color);
    // Draw some shapes
    $white = imagecolorallocate($im, 255, 255, 255);
    imagestring($im, 5, 20, 20, "Lovina Real Estate Test Image", $white);
    imagejpeg($im, $path, 90);
    imagedestroy($im);
}

function createTestPng($path, $w, $h, $r, $g, $b) {
    $im = imagecreatetruecolor($w, $h);
    $color = imagecolorallocate($im, $r, $g, $b);
    imagefilledrectangle($im, 0, 0, $w, $h, $color);
    $white = imagecolorallocate($im, 255, 255, 255);
    imagestring($im, 5, 20, 20, "PNG Test Image", $white);
    imagepng($im, $path);
    imagedestroy($im);
}

$testFilenames = [
    'WhatsApp Image 2026-09-19 at 14.20.55.jpeg',
    'IMG_0091.JPG',
    'DSC_8821.PNG',
    'photo 123.jpg',
    'Large_Camera_3000x2000.jpg'
];

$filesToTest = [];

createTestJpeg($tempDir . '/WhatsApp Image 2026-09-19 at 14.20.55.jpeg', 1200, 800, 50, 100, 200);
createTestJpeg($tempDir . '/IMG_0091.JPG', 1600, 1067, 200, 100, 50);
createTestPng($tempDir . '/DSC_8821.PNG', 1000, 750, 100, 200, 100);
createTestJpeg($tempDir . '/photo 123.jpg', 800, 600, 150, 150, 50);
createTestJpeg($tempDir . '/Large_Camera_3000x2000.jpg', 3000, 2000, 70, 70, 180);

echo "=== TEST 1: TESTING PROPERTY IMAGE SERVICE DIRECTLY WITH 5 FILENAME TYPES ===\n";
$service = app(PropertyImageService::class);

foreach ($testFilenames as $idx => $origName) {
    $seq = $idx + 1;
    $filePath = $tempDir . '/' . $origName;
    $mime = str_ends_with(strtolower($origName), '.png') ? 'image/png' : 'image/jpeg';
    $uploadedFile = new UploadedFile($filePath, $origName, $mime, null, true);

    $result = $service->processAndStore($uploadedFile, $property, $seq);

    echo "--- Image {$seq}: {$origName} ---\n";
    if (!$result) {
        echo "FAIL: Result is null\n";
        continue;
    }

    $finalPath = $result['path'];
    $filename = $result['filename'];
    $alt = $result['alt'];

    echo "Generated Filename: {$filename}\n";
    echo "Stored Relative Path: {$finalPath}\n";
    echo "Generated Alt Text: {$alt}\n";

    // Checks:
    $isLowercase = ($filename === strtolower($filename));
    $isWebp = str_ends_with($filename, '.webp');
    $containsSlug = str_starts_with($filename, $property->slug);
    $notOriginal = ($filename !== $origName);

    echo "Checks:\n";
    echo " - Not original filename? " . ($notOriginal ? "PASS" : "FAIL") . "\n";
    echo " - Lowercase? " . ($isLowercase ? "PASS" : "FAIL") . "\n";
    echo " - Has .webp extension? " . ($isWebp ? "PASS" : "FAIL") . "\n";
    echo " - Contains property slug? " . ($containsSlug ? "PASS" : "FAIL") . "\n";

    // Check file on disk
    $absDiskPath = Storage::disk('public')->path($finalPath);
    $fileExists = file_exists($absDiskPath);
    echo " - Exists in public storage? " . ($fileExists ? "PASS ({$absDiskPath})" : "FAIL") . "\n";

    if ($fileExists) {
        $imgInfo = @getimagesize($absDiskPath);
        $w = $imgInfo[0] ?? 0;
        $h = $imgInfo[1] ?? 0;
        $type = $imgInfo['mime'] ?? 'unknown';
        echo " - WebP Validated Dimensions: {$w}x{$h} (MIME: {$type})\n";

        if ($origName === 'Large_Camera_3000x2000.jpg') {
            $scaledDown = ($w <= 2048 && $h <= 2048);
            $aspectRatioPreserved = (abs(($w / $h) - (3000 / 2000)) < 0.02);
            echo " - Scaled to max 2048? " . ($scaledDown ? "PASS ({$w}x{$h})" : "FAIL") . "\n";
            echo " - Aspect ratio preserved? " . ($aspectRatioPreserved ? "PASS" : "FAIL") . "\n";
        }
    }
    echo "\n";
}

echo "=== DIRECT SERVICE TESTS COMPLETE ===\n";
