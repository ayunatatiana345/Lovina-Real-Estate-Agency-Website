<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use Illuminate\Http\Request;

echo "=== TESTING IMAGE URL GENERATION UNDER REQUEST (http://127.0.0.1:8000) ===\n\n";

$request = Request::create('http://127.0.0.1:8000/admin/properties/137/edit', 'GET');
app()->instance('request', $request);

// Test Property 137
$p137 = Property::with('images')->find(137);
echo "Property #137: {$p137->name}\n";
echo "Total Images: " . $p137->images->count() . "\n";
foreach ($p137->images as $img) {
    echo " - Image #{$img->id} (is_cover=" . ($img->is_cover ? '1' : '0') . "): {$img->image_url}\n";
}
echo "Property #137 real_cover_image_url: " . $p137->real_cover_image_url . "\n\n";

// Test Property 148
$p148 = Property::with('images')->find(148);
echo "Property #148: {$p148->name}\n";
echo "Total Images: " . $p148->images->count() . "\n";
foreach ($p148->images as $img) {
    echo " - Image #{$img->id} (is_cover=" . ($img->is_cover ? '1' : '0') . "): {$img->image_url}\n";
}
echo "Property #148 real_cover_image_url: " . $p148->real_cover_image_url . "\n";
