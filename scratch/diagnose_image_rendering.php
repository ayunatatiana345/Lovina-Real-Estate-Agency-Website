<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage;

echo "=== DIAGNOSTIC INSPECTION ===" . PHP_EOL;

$p = Property::with('images')->find(137);
echo "Property #137 Name: {$p->name}" . PHP_EOL;

foreach ($p->images as $img) {
    echo "Image ID #{$img->id}:" . PHP_EOL;
    echo "  image_path in DB: {$img->image_path}" . PHP_EOL;
    echo "  image_url accessor: {$img->image_url}" . PHP_EOL;
    echo "  Storage::disk('public')->url: " . Storage::disk('public')->url($img->image_path) . PHP_EOL;
    echo "  asset('storage/' . path): " . asset('storage/' . $img->image_path) . PHP_EOL;
    
    $fullStorageAppPublic = storage_path('app/public/' . $img->image_path);
    $fullPublicStorage = public_path('storage/' . $img->image_path);
    
    echo "  Physical storage_path: {$fullStorageAppPublic} (Exists: " . (file_exists($fullStorageAppPublic) ? 'YES' : 'NO') . ")" . PHP_EOL;
    echo "  Physical public_path: {$fullPublicStorage} (Exists: " . (file_exists($fullPublicStorage) ? 'YES' : 'NO') . ")" . PHP_EOL;
}

echo PHP_EOL . "=== STORAGE SYMLINK CHECK ===" . PHP_EOL;
$symlinkPath = public_path('storage');
echo "public/storage path: {$symlinkPath}" . PHP_EOL;
echo "is_dir: " . (is_dir($symlinkPath) ? 'YES' : 'NO') . PHP_EOL;
echo "is_link: " . (is_link($symlinkPath) ? 'YES' : 'NO') . PHP_EOL;
echo "readlink: " . (is_link($symlinkPath) ? readlink($symlinkPath) : 'N/A') . PHP_EOL;

echo PHP_EOL . "=== ENV APP_URL CHECK ===" . PHP_EOL;
echo "env('APP_URL'): " . env('APP_URL') . PHP_EOL;
echo "config('app.url'): " . config('app.url') . PHP_EOL;
echo "config('filesystems.disks.public.url'): " . config('filesystems.disks.public.url') . PHP_EOL;
