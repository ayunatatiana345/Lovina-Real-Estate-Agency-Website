<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage;

$out = "=== ALL PROPERTIES AUDIT ===" . PHP_EOL;
$properties = Property::with('images')->get();

foreach ($properties as $p) {
    $out .= "Property #{$p->id}: {$p->name} (Slug: {$p->slug})" . PHP_EOL;
    $out .= "  Total Images in DB: " . $p->images->count() . PHP_EOL;
    $cover = $p->images->firstWhere('is_cover', true);
    $out .= "  Cover Image in DB: " . ($cover ? "ID #{$cover->id} ({$cover->image_path})" : "NONE") . PHP_EOL;
    $out .= "  real_cover_image_url: " . ($p->real_cover_image_url ?: 'NULL') . PHP_EOL;
    $out .= "  primary_image_url: " . ($p->primary_image_url ?: 'NULL') . PHP_EOL;

    foreach ($p->images as $img) {
        $storageExists = Storage::disk('public')->exists($img->image_path) ? 'YES' : 'NO';
        $pubStorageExists = file_exists(public_path('storage/' . $img->image_path)) ? 'YES' : 'NO';
        $pubImgExists = file_exists(public_path('images/' . basename($img->image_path))) ? 'YES' : 'NO';
        $pubDirectExists = file_exists(public_path($img->image_path)) ? 'YES' : 'NO';
        
        $out .= "    - Image #{$img->id}: is_cover=" . ($img->is_cover ? '1' : '0') . " | sort_order={$img->sort_order}" . PHP_EOL;
        $out .= "      Path: {$img->image_path}" . PHP_EOL;
        $out .= "      image_url: " . ($img->image_url ?: 'NULL') . PHP_EOL;
        $out .= "      Physical file: Storage={$storageExists}, public/storage={$pubStorageExists}, public/images={$pubImgExists}, public/path={$pubDirectExists}" . PHP_EOL;
    }
    $out .= "----------------------------------------" . PHP_EOL;
}

$out .= PHP_EOL . "=== ORPHAN PROPERTY IMAGES (images in DB with non-existent property_id) ===" . PHP_EOL;
$orphanImages = PropertyImage::whereNotIn('property_id', $properties->pluck('id'))->get();
$out .= "Total Orphan Images in DB: " . $orphanImages->count() . PHP_EOL;
foreach ($orphanImages as $oi) {
    $out .= "  - Orphan Image #{$oi->id}: property_id={$oi->property_id}, path={$oi->image_path}" . PHP_EOL;
}

$out .= PHP_EOL . "=== STORAGE DIRECTORY SCAN ===" . PHP_EOL;
$filesInStorage = Storage::disk('public')->allFiles();
$out .= "Total files in public storage: " . count($filesInStorage) . PHP_EOL;
foreach ($filesInStorage as $f) {
    $out .= "  - Storage file: {$f}" . PHP_EOL;
}

file_put_contents(__DIR__ . '/audit_output.txt', $out);
echo "Written to scratch/audit_output.txt\n";
