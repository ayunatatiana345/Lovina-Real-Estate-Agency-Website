<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Location;
use App\Models\Property;
use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Services\LocationImageService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

echo "========================================================\n";
echo "HTTP CONTROLLER SIMULATION TEST\n";
echo "========================================================\n";

$controller = app(AdminLocationController::class);
$imageService = app(LocationImageService::class);

// 1. Test Admin store() via Controller method
echo "\n1. Testing store()...\n";
$tmpFile = tempnam(sys_get_temp_dir(), 'test_http_') . '.jpg';
$gd = imagecreatetruecolor(400, 300);
imagejpeg($gd, $tmpFile);
imagedestroy($gd);

$uploadedFile = new UploadedFile($tmpFile, 'http_test.jpg', 'image/jpeg', null, true);

$storeRequest = Request::create('/admin/locations', 'POST', [
    'name' => 'Http Controller Test Location',
    'description' => 'A wonderful location created via controller request test.',
    'is_popular' => '1',
    'status' => 'active',
], [], ['image' => $uploadedFile]);

// Bind session
$session = app('session.store');
$storeRequest->setLaravelSession($session);

$response = $controller->store($storeRequest, $imageService);
echo "  -> Store response status: " . $response->getStatusCode() . "\n";
echo "  -> Redirect target: " . $response->getTargetUrl() . "\n";

$createdLoc = Location::where('name', 'Http Controller Test Location')->first();
if (!$createdLoc) {
    echo "  [FAIL] Created location not found in DB!\n";
    exit(1);
}
echo "  -> Created ID: {$createdLoc->id} | Image: {$createdLoc->image}\n";
echo "  [PASS] Store controller method succeeded!\n";

// 2. Test Admin update() with remove_image = 1 via Controller method
echo "\n2. Testing update() with remove_image=1...\n";
$updateRequest = Request::create("/admin/locations/{$createdLoc->id}", 'PUT', [
    'name' => 'Http Controller Test Location (No Image)',
    'description' => 'Updated description without image.',
    'remove_image' => '1',
    'status' => 'active',
]);
$updateRequest->setLaravelSession($session);

$updateResponse = $controller->update($updateRequest, $createdLoc->id, $imageService);
echo "  -> Update response status: " . $updateResponse->getStatusCode() . "\n";

$reloadedLoc = $createdLoc->fresh();
echo "  -> Reloaded Name: {$reloadedLoc->name}\n";
echo "  -> Reloaded Image: " . var_export($reloadedLoc->image, true) . "\n";

if ($reloadedLoc->image !== null || $reloadedLoc->name !== 'Http Controller Test Location (No Image)') {
    echo "  [FAIL] Update controller method failed to clear image or update name!\n";
    exit(1);
}
echo "  [PASS] Update controller method cleared image and updated database!\n";

// 3. Test Admin destroy() via Controller method
echo "\n3. Testing destroy()...\n";
$destroyResponse = $controller->destroy($reloadedLoc->id, $imageService);
echo "  -> Destroy response status: " . $destroyResponse->getStatusCode() . "\n";

$deletedLoc = Location::find($reloadedLoc->id);
if ($deletedLoc) {
    echo "  [FAIL] Destroy controller method did not delete location!\n";
    exit(1);
}
echo "  [PASS] Destroy controller method deleted record successfully!\n";

@unlink($tmpFile);

echo "\n========================================================\n";
echo "ALL CONTROLLER TESTS PASSED SUCCESSFULLY!\n";
echo "========================================================\n";
