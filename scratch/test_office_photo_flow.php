<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CompanySetting;
use App\Services\BrandingImageService;
use App\Http\Controllers\Admin\CompanySettingController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;

echo "========================================================\n";
echo "STARTING OFFICE PHOTO MANAGEMENT FULL FLOW TEST\n";
echo "========================================================\n";

$brandingService = app(BrandingImageService::class);
$controller = app(CompanySettingController::class);

// Snapshot initial branding values
$initialSettings = CompanySetting::getSettings();
$initialLogoPrimary = $initialSettings->logo_primary;
$initialLogoAlt = $initialSettings->logo_alt;
$initialFavicon = $initialSettings->favicon;

echo "Initial branding:\n";
echo "  - logo_primary: {$initialLogoPrimary}\n";
echo "  - logo_alt: {$initialLogoAlt}\n";
echo "  - favicon: {$initialFavicon}\n";
echo "  - office_photo: " . var_export($initialSettings->office_photo, true) . "\n\n";

// 1. Create a dummy test office photo (JPG)
$tmpImgPath1 = tempnam(sys_get_temp_dir(), 'test_off1_') . '.jpg';
$gd1 = imagecreatetruecolor(1200, 800);
$bg1 = imagecolorallocate($gd1, 30, 90, 180);
imagefilledrectangle($gd1, 0, 0, 1200, 800, $bg1);
imagejpeg($gd1, $tmpImgPath1, 90);
imagedestroy($gd1);

$uploadedFile1 = new UploadedFile($tmpImgPath1, 'office_test_1.jpg', 'image/jpeg', null, true);

// TEST 1: UPLOAD & PROCESS OFFICE PHOTO VIA BRANDING SERVICE
echo "[TEST 1] Uploading new office photo...\n";
$processedPath1 = $brandingService->processAndStoreOfficePhoto($uploadedFile1);
echo "  -> Processed image path: {$processedPath1}\n";

if (!$processedPath1 || !Str::startsWith($processedPath1, 'branding/lovina-north-bali-real-estate-office-') || !Str::endsWith($processedPath1, '.webp')) {
    echo "  [FAIL] Office photo filename format or extension is invalid!\n";
    exit(1);
}

$diskPath1 = Storage::disk('public')->path($processedPath1);
if (!file_exists($diskPath1)) {
    echo "  [FAIL] Physical WebP file not found on disk!\n";
    exit(1);
}
echo "  -> Physical file exists: YES (" . filesize($diskPath1) . " bytes)\n";
echo "  [PASS] WebP image processed and stored with SEO filename!\n\n";

// TEST 2: CONTROLLER UPDATE & DATABASE PERSISTENCE
echo "[TEST 2] Updating Company Settings with office photo via Controller...\n";
$session = app('session.store');

$updateReq1 = Request::create('/admin/settings', 'POST', [
    'company_name' => $initialSettings->company_name,
    'site_title' => $initialSettings->site_title,
    'tagline' => $initialSettings->tagline,
    'phone' => $initialSettings->phone,
    'whatsapp' => $initialSettings->whatsapp,
    'email' => $initialSettings->email,
    'address' => $initialSettings->address,
], [], ['office_photo' => $uploadedFile1]);
$updateReq1->setLaravelSession($session);

$res1 = $controller->update($updateReq1, $brandingService);
$reloaded1 = CompanySetting::getSettings()->fresh();

echo "  -> DB office_photo: {$reloaded1->office_photo}\n";
echo "  -> has_office_photo: " . ($reloaded1->has_office_photo ? 'TRUE' : 'FALSE') . "\n";
echo "  -> office_photo_url: {$reloaded1->office_photo_url}\n";

if (!$reloaded1->office_photo || !$reloaded1->has_office_photo) {
    echo "  [FAIL] Controller update failed to save office_photo in DB!\n";
    exit(1);
}
echo "  [PASS] Office photo persisted to database and accessor working!\n\n";

// TEST 3: REPLACE OFFICE PHOTO (VERIFY OLD FILE DELETED & UNRELATED UNTOUCHED)
echo "[TEST 3] Replacing office photo with a new image...\n";
$firstStoredFile = $reloaded1->office_photo;

$tmpImgPath2 = tempnam(sys_get_temp_dir(), 'test_off2_') . '.png';
$gd2 = imagecreatetruecolor(1000, 600);
$bg2 = imagecolorallocate($gd2, 220, 120, 40);
imagefilledrectangle($gd2, 0, 0, 1000, 600, $bg2);
imagepng($gd2, $tmpImgPath2);
imagedestroy($gd2);

$uploadedFile2 = new UploadedFile($tmpImgPath2, 'office_test_2.png', 'image/png', null, true);

$updateReq2 = Request::create('/admin/settings', 'POST', [
    'company_name' => $initialSettings->company_name,
    'site_title' => $initialSettings->site_title,
    'tagline' => $initialSettings->tagline,
    'phone' => $initialSettings->phone,
    'whatsapp' => $initialSettings->whatsapp,
    'email' => $initialSettings->email,
    'address' => $initialSettings->address,
], [], ['office_photo' => $uploadedFile2]);
$updateReq2->setLaravelSession($session);

$res2 = $controller->update($updateReq2, $brandingService);
$reloaded2 = CompanySetting::getSettings()->fresh();

echo "  -> New DB office_photo: {$reloaded2->office_photo}\n";
$oldFileExists = Storage::disk('public')->exists($firstStoredFile);
$newFileExists = Storage::disk('public')->exists($reloaded2->office_photo);

echo "  -> Old file deleted from disk: " . (!$oldFileExists ? "YES" : "NO") . "\n";
echo "  -> New file exists on disk: " . ($newFileExists ? "YES" : "NO") . "\n";

// Check unrelated branding untouched
echo "  -> logo_primary unchanged: " . ($reloaded2->logo_primary === $initialLogoPrimary ? "YES" : "NO") . "\n";
echo "  -> logo_alt unchanged: " . ($reloaded2->logo_alt === $initialLogoAlt ? "YES" : "NO") . "\n";
echo "  -> favicon unchanged: " . ($reloaded2->favicon === $initialFavicon ? "YES" : "NO") . "\n";

if ($oldFileExists || !$newFileExists || $reloaded2->logo_primary !== $initialLogoPrimary) {
    echo "  [FAIL] Replace flow failed or affected unrelated branding!\n";
    exit(1);
}
echo "  [PASS] Replace office photo succeeded without touching other settings!\n\n";

// TEST 4: REMOVE OFFICE PHOTO
echo "[TEST 4] Removing office photo (remove_office_photo = 1)...\n";
$fileToRemove = $reloaded2->office_photo;

$updateReq3 = Request::create('/admin/settings', 'POST', [
    'company_name' => $initialSettings->company_name,
    'site_title' => $initialSettings->site_title,
    'tagline' => $initialSettings->tagline,
    'phone' => $initialSettings->phone,
    'whatsapp' => $initialSettings->whatsapp,
    'email' => $initialSettings->email,
    'address' => $initialSettings->address,
    'remove_office_photo' => '1',
]);
$updateReq3->setLaravelSession($session);

$res3 = $controller->update($updateReq3, $brandingService);
$reloaded3 = CompanySetting::getSettings()->fresh();

echo "  -> DB office_photo after removal: " . var_export($reloaded3->office_photo, true) . "\n";
$removedExists = Storage::disk('public')->exists($fileToRemove);
echo "  -> Physical file deleted: " . (!$removedExists ? "YES" : "NO") . "\n";
echo "  -> has_office_photo: " . ($reloaded3->has_office_photo ? 'TRUE' : 'FALSE') . " (expected FALSE)\n";
echo "  -> office_photo_url: " . var_export($reloaded3->office_photo_url, true) . " (expected NULL)\n";

if ($reloaded3->office_photo !== null || $removedExists || $reloaded3->has_office_photo !== false) {
    echo "  [FAIL] Removal flow failed to clear DB or disk!\n";
    exit(1);
}
echo "  [PASS] Office photo removed cleanly from DB and storage!\n\n";

// TEST 5: BLADE VIEW SYNCHRONIZATION TEST
echo "[TEST 5] Verifying Blade views rendering with & without office photo...\n";
view()->share('errors', new ViewErrorBag);

// Render without photo
$properties = \App\Models\Property::where('status', 'published')->take(5)->get();
$contactHtmlNoPhoto = view('public.contact', ['settings' => $reloaded3, 'properties' => $properties])->render();

if (!str_contains($contactHtmlNoPhoto, 'Office Photo') || !str_contains($contactHtmlNoPhoto, 'data-lucide="building"')) {
    echo "  [FAIL] Fallback placeholder not found in Contact Us HTML!\n";
    exit(1);
}
echo "  -> Contact Us without photo renders placeholder icon properly: YES\n";

// Now set photo and re-render
$testSavedPath = $brandingService->processAndStoreOfficePhoto($uploadedFile1);
$reloaded3->update(['office_photo' => $testSavedPath]);
$settingsWithPhoto = $reloaded3->fresh();

$contactHtmlWithPhoto = view('public.contact', ['settings' => $settingsWithPhoto, 'properties' => $properties])->render();
if (!str_contains($contactHtmlWithPhoto, $settingsWithPhoto->office_photo_url)) {
    echo "  [FAIL] Office photo URL not found in Contact Us HTML when photo is set!\n";
    exit(1);
}
echo "  -> Contact Us with photo renders actual image URL properly: YES\n";

// Also test admin settings view
$currencyService = app(\App\Services\CurrencyService::class);
$currencyMeta = $currencyService->getRateMetadata();
$adminSettingsHtml = view('admin.settings.index', ['settings' => $settingsWithPhoto, 'currencyMeta' => $currencyMeta])->render();

if (!str_contains($adminSettingsHtml, 'prev-office-photo-img') || !str_contains($adminSettingsHtml, 'office_photo')) {
    echo "  [FAIL] Admin settings view does not contain Office Photo controls!\n";
    exit(1);
}
echo "  -> Admin settings view renders Office Photo controls & preview properly: YES\n";

@unlink($tmpImgPath1);
@unlink($tmpImgPath2);

echo "\n========================================================\n";
echo "ALL OFFICE PHOTO TESTS PASSED 100% SUCCESSFULLY!\n";
echo "========================================================\n";
