<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\Location;

$prop = Property::find(14);
if ($prop) {
    echo "ID: {$prop->id}\n";
    echo "Name: {$prop->name}\n";
    echo "Slug: {$prop->slug}\n";
    echo "Location ID: {$prop->location_id}\n";
    echo "Location Name: " . ($prop->location ? $prop->location->name : 'N/A') . "\n";
    echo "Location Status: " . ($prop->location ? $prop->location->status : 'N/A') . "\n";
    echo "Description: {$prop->description}\n";
    echo "Short Description: {$prop->short_description}\n";
    echo "Price: {$prop->price}\n";
    echo "Category ID: {$prop->category_id}\n";
} else {
    echo "Property ID 14 not found.\n";
}
