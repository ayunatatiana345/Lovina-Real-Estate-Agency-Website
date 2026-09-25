<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CompanySetting;

$settings = CompanySetting::first();
if ($settings) {
    $settings->update([
        'logo_primary' => 'images/black logo lovina.png',
        'logo_alt' => 'images/white logo lovina.png',
        'favicon' => 'favicon.ico',
    ]);
    echo "Updated existing settings row.\n";
} else {
    CompanySetting::getSettings();
    echo "Created initial settings row.\n";
}

$fresh = CompanySetting::first();
echo "Stored DB values:\n";
echo " - logo_primary: {$fresh->logo_primary}\n";
echo " - logo_alt: {$fresh->logo_alt}\n";
echo " - favicon: {$fresh->favicon}\n";
echo "\nAccessor URLs:\n";
echo " - logo_primary_url: {$fresh->logo_primary_url}\n";
echo " - logo_alt_url: {$fresh->logo_alt_url}\n";
echo " - favicon_url: {$fresh->favicon_url}\n";
