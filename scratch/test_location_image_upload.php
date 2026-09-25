<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$consoleKernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$consoleKernel->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

echo "=== TESTING LOCATION IMAGE UPLOAD & DISPLAY ===\n";

$admin = User::first();
if ($admin) {
    \Illuminate\Support\Facades\Auth::login($admin);
}

// Helper to create a dummy image (PNG)
function createTestImageFile($name, $width = 800, $height = 600, $r = 30, $g = 144, $b = 255) {
    $tempPath = sys_get_temp_dir() . '/' . $name . '_' . uniqid() . '.png';
    $img = imagecreatetruecolor($width, $height);
    $color = imagecolorallocate($img, $r, $g, $b);
    imagefilledrectangle($img, 0, 0, $width, $height, $color);
    
    // add some text
    $textColor = imagecolorallocate($img, 255, 255, 255);
    imagestring($img, 5, 20, 20, $name, $textColor);
    
    imagepng($img, $tempPath);
    imagedestroy($img);
    
    return new UploadedFile($tempPath, $name . '.png', 'image/png', null, true);
}

// 3 Test Locations
$testLocations = [
    'Lovina' => 'lovina-location-01.webp',
    'Celuk Buluh' => 'celuk-buluh-location-01.webp',
    'Sing-Sing' => 'sing-sing-location-01.webp',
];

foreach ($testLocations as $locName => $expectedFilename) {
    echo "\n---------------------------------------------\n";
    echo "Testing Location: '{$locName}'\n";
    echo "Expected Filename: '{$expectedFilename}'\n";
    
    $loc = Location::where('name', $locName)->first();
    if (!$loc) {
        echo "ERROR: Location '{$locName}' not found in database.\n";
        continue;
    }
    
    // Start session and generate token
    $session = $app->make('session')->driver();
    $session->start();
    $token = $session->token();

    // Create test image file
    $uploadedFile = createTestImageFile(str_replace([' ', '-'], '_', $locName), 1200, 800);
    
    // Simulate Admin PUT update request
    $request = Request::create(
        '/admin/locations/' . $loc->id,
        'POST',
        [
            '_method' => 'PUT',
            '_token' => $token,
            'name' => $loc->name,
            'description' => $loc->description,
            'status' => $loc->status,
            'is_popular' => $loc->is_popular ? '1' : '0',
        ],
        [],
        [
            'image' => $uploadedFile,
        ]
    );
    $request->setLaravelSession($session);
    
    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);
    
    echo "Update Response Status: " . $response->getStatusCode() . "\n";
    
    $loc->refresh();
    echo "Stored DB image value: '{$loc->image}'\n";
    
    // 1. Verify DB path
    $expectedPath = 'locations/' . $expectedFilename;
    $dbMatch = ($loc->image === $expectedPath);
    echo " - DB relative path match: " . ($dbMatch ? "PASSED ({$loc->image})" : "FAILED (got {$loc->image}, expected {$expectedPath})") . "\n";
    
    // 2. Verify physical file exists on disk
    $disk = Storage::disk('public');
    $physicalExists = $disk->exists($loc->image);
    $fullPath = $disk->path($loc->image);
    echo " - Physical file on disk exists: " . ($physicalExists ? "PASSED ({$fullPath})" : "FAILED") . "\n";
    
    // 3. Verify format is WebP
    if ($physicalExists) {
        $info = @getimagesize($fullPath);
        $isWebP = ($info && $info['mime'] === 'image/webp');
        echo " - Converted to WebP format: " . ($isWebP ? "PASSED (mime: {$info['mime']}, {$info[0]}x{$info[1]})" : "FAILED") . "\n";
    }
    
    // 4. Verify Model Accessors
    echo " - loc->image_url: {$loc->image_url}\n";
    echo " - loc->has_image: " . ($loc->has_image ? "TRUE (PASSED)" : "FALSE (FAILED)") . "\n";
    
    // 5. Test Admin Page HTML
    $adminReq = Request::create('/admin/locations', 'GET');
    $adminReq->setLaravelSession($session);
    $adminRes = $kernel->handle($adminReq);
    $adminHtml = $adminRes->getContent();
    $adminContainsImage = str_contains($adminHtml, $expectedFilename);
    echo " - Admin table contains WebP image: " . ($adminContainsImage ? "PASSED" : "FAILED") . "\n";
    
    // 6. Test Public Locations Index HTML
    $pubReq = Request::create('/locations', 'GET');
    $pubRes = $kernel->handle($pubReq);
    $pubHtml = $pubRes->getContent();
    $pubContainsImage = str_contains($pubHtml, $expectedFilename);
    echo " - Public Locations index contains image: " . ($pubContainsImage ? "PASSED" : "FAILED") . "\n";
    
    // 7. Test Public Location Show HTML
    $showReq = Request::create('/locations/' . $loc->slug, 'GET');
    $showRes = $kernel->handle($showReq);
    echo " - Public Location show page status: " . $showRes->getStatusCode() . " -> " . ($showRes->getStatusCode() === 200 ? "PASSED" : "FAILED") . "\n";
}

echo "\n=== ALL VERIFICATION TESTS FINISHED ===\n";
