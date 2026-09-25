<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage;

foreach (PropertyImage::where('property_id', 148)->get() as $img) {
    Storage::disk('public')->delete($img->image_path);
    $img->delete();
}

// Also remove test directory if empty
Storage::disk('public')->deleteDirectory('properties/four-bedroom-villa-in-sing-sing');

echo "CLEANUP_SUCCESS. Current image count: " . PropertyImage::count() . "\n";
