<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$consoleKernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$consoleKernel->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

$admin = User::first();
$request = Request::create('/admin/settings', 'GET');
$session = $app->make('session')->driver();
$session->start();
$request->setLaravelSession($session);
if ($admin) {
    \Illuminate\Support\Facades\Auth::login($admin);
}

$response = $kernel->handle($request);
$content = $response->getContent();

echo "Admin Settings HTTP Status: " . $response->getStatusCode() . "\n";

// Check if image sources are present
$hasPrimary = str_contains($content, 'images/black logo lovina.png');
$hasAlt = str_contains($content, 'images/white logo lovina.png');
$hasFavicon = str_contains($content, 'favicon.ico');

echo "Primary Logo present in preview: " . ($hasPrimary ? "YES (PASSED)" : "NO (FAILED)") . "\n";
echo "Alt Logo present in preview: " . ($hasAlt ? "YES (PASSED)" : "NO (FAILED)") . "\n";
echo "Favicon present in preview: " . ($hasFavicon ? "YES (PASSED)" : "NO (FAILED)") . "\n";

// Public site check
$pubRequest = Request::create('/', 'GET');
$pubResponse = $kernel->handle($pubRequest);
$pubContent = $pubResponse->getContent();

echo "\nPublic Home HTTP Status: " . $pubResponse->getStatusCode() . "\n";
echo "Public navbar logo present: " . (str_contains($pubContent, 'images/black logo lovina.png') ? "YES (PASSED)" : "NO (FAILED)") . "\n";
echo "Public footer logo present: " . (str_contains($pubContent, 'images/white logo lovina.png') ? "YES (PASSED)" : "NO (FAILED)") . "\n";
echo "Public favicon present: " . (str_contains($pubContent, 'favicon.ico') ? "YES (PASSED)" : "NO (FAILED)") . "\n";
