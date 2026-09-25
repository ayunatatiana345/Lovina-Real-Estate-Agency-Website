<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;

$p = Property::with('images')->find(137);
foreach ($p->images as $img) {
    echo "ID: {$img->id} | sort_order: {$img->sort_order} | is_cover: " . ($img->is_cover ? '1 (COVER)' : '0') . " | path: {$img->image_path}\n";
}
