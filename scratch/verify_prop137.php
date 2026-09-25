<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$prop137 = App\Models\Property::with('images')->find(137);
echo "Property #137 images count: " . $prop137->images->count() . "\n";
foreach ($prop137->images as $img) {
    $exists = file_exists(public_path('storage/' . $img->image_path));
    echo "ID: {$img->id}, Order: {$img->sort_order}, Cover: " . ($img->is_cover ? 'YES' : 'NO') . ", Exists: " . ($exists ? 'YES' : 'NO') . ", Path: {$img->image_path}\n";
}
