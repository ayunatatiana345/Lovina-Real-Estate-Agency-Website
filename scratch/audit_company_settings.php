<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CompanySetting;
use Illuminate\Support\Facades\DB;

$raw = DB::table('company_settings')->first();
echo "--- RAW DB RECORD ---\n";
print_r($raw);

echo "\n--- FILE CHECKS ---\n";
$checkFiles = [
    'public/images/black logo lovina.png' => public_path('images/black logo lovina.png'),
    'public/images/white logo lovina.png' => public_path('images/white logo lovina.png'),
    'public/images/logo-placeholder.png' => public_path('images/logo-placeholder.png'),
    'public/images/logo-alt-placeholder.png' => public_path('images/logo-alt-placeholder.png'),
    'public/images/favicon-placeholder.png' => public_path('images/favicon-placeholder.png'),
    'public/favicon.ico' => public_path('favicon.ico'),
    'storage/app/public' => storage_path('app/public'),
    'public/storage' => public_path('storage'),
];

foreach ($checkFiles as $label => $path) {
    echo "$label exists: " . (file_exists($path) ? "YES" : "NO") . " (path: $path)\n";
}

if ($raw) {
    if (!empty($raw->logo_primary)) {
        echo "raw logo_primary exists in storage: " . (file_exists(storage_path('app/public/' . $raw->logo_primary)) ? "YES" : "NO") . "\n";
        echo "raw logo_primary exists in public: " . (file_exists(public_path($raw->logo_primary)) ? "YES" : "NO") . "\n";
    }
    if (!empty($raw->logo_alt)) {
        echo "raw logo_alt exists in storage: " . (file_exists(storage_path('app/public/' . $raw->logo_alt)) ? "YES" : "NO") . "\n";
        echo "raw logo_alt exists in public: " . (file_exists(public_path($raw->logo_alt)) ? "YES" : "NO") . "\n";
    }
    if (!empty($raw->favicon)) {
        echo "raw favicon exists in storage: " . (file_exists(storage_path('app/public/' . $raw->favicon)) ? "YES" : "NO") . "\n";
        echo "raw favicon exists in public: " . (file_exists(public_path($raw->favicon)) ? "YES" : "NO") . "\n";
    }
}
