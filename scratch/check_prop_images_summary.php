<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\PropertyImage;

echo "Properties with images in DB:\n";
$props = Property::with('images')->get();
$withImages = 0;
$withoutImages = 0;

foreach ($props as $p) {
    if ($p->images->count() > 0) {
        $withImages++;
        echo "Property #{$p->id} ({$p->name}) has {$p->images->count()} images. Cover: " . ($p->coverImage ? "ID #{$p->coverImage->id}" : "NONE") . "\n";
    } else {
        $withoutImages++;
    }
}

echo "\nSummary: {$withImages} properties WITH images, {$withoutImages} properties WITHOUT images.\n";
echo "Total PropertyImage records in DB: " . PropertyImage::count() . "\n";
