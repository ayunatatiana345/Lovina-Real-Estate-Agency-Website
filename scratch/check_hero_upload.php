<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CmsContent;
use App\Services\HeroImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

echo "=== TEST 1: HeroImageService SEO Filename & Genuine WebP ===\n";
$service = new HeroImageService();

// Create temp JPEG file named IMG_8392.jpg
$tmpPath = tempnam(sys_get_temp_dir(), 'hero_test');
$im = imagecreatetruecolor(1920, 800);
$bg = imagecolorallocate($im, 30, 58, 138);
imagefilledrectangle($im, 0, 0, 1920, 800, $bg);
imagejpeg($im, $tmpPath);
imagedestroy($im);

$uploadedFile = new UploadedFile($tmpPath, 'IMG_8392.jpg', 'image/jpeg', null, true);
$storedPath = $service->processAndStore($uploadedFile);

echo "Input: IMG_8392.jpg\n";
echo "Output path: {$storedPath}\n";

$isExpectedPattern = preg_match('/^cms\/north-bali-real-estate-hero-[a-z0-9]{4}\.webp$/', $storedPath);
echo "Filename matches pattern: " . ($isExpectedPattern ? "PASS" : "FAIL") . "\n";

$fullDiskPath = Storage::disk('public')->path($storedPath);
echo "Physical file exists: " . (file_exists($fullDiskPath) ? "PASS" : "FAIL") . "\n";

$imgInfo = getimagesize($fullDiskPath);
echo "Actual MIME type: " . $imgInfo['mime'] . " (" . ($imgInfo['mime'] === 'image/webp' ? "GENUINE WEBP" : "NOT WEBP") . ")\n";
echo "Dimensions: {$imgInfo[0]}x{$imgInfo[1]}\n";

echo "\n=== TEST 2: Database Storage & Public URL ===\n";
$heroContent = CmsContent::getContent('homepage', 'hero', []);
$heroContent['background_image'] = $storedPath;
CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'hero'], ['content' => $heroContent]);

$dbContent = CmsContent::getContent('homepage', 'hero');
echo "DB background_image: " . $dbContent['background_image'] . "\n";
echo "Public URL helper: " . asset('storage/' . $dbContent['background_image']) . "\n";

echo "\n=== TEST 3: Cleanup / Removal ===\n";
$deleted = $service->deleteOldHeroImage($storedPath);
echo "Old file deleted: " . ($deleted ? "PASS" : "FAIL") . "\n";
echo "Physical file still on disk: " . (file_exists($fullDiskPath) ? "YES (FAIL)" : "NO (PASS)") . "\n";

$heroContent['background_image'] = null;
CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'hero'], ['content' => $heroContent]);
$clearedDb = CmsContent::getContent('homepage', 'hero');
echo "Cleared DB background_image: " . var_export($clearedDb['background_image'], true) . "\n";

if (file_exists($tmpPath)) {
    @unlink($tmpPath);
}

echo "\nAll verification steps completed.\n";
