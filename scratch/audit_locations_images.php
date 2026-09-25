<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Location;

$locations = Location::all();
echo "TOTAL LOCATIONS: " . $locations->count() . "\n";
foreach ($locations as $loc) {
    $storageExists = $loc->image && file_exists(storage_path('app/public/' . $loc->image));
    $publicExists = $loc->image && file_exists(public_path($loc->image));
    echo "ID: {$loc->id} | Name: '{$loc->name}' | Slug: {$loc->slug} | Image: '{$loc->image}' | Storage: " . ($storageExists ? "YES" : "NO") . " | Public: " . ($publicExists ? "YES" : "NO") . "\n";
}
