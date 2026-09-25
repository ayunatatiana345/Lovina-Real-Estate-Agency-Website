<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\DB;

$imagesCount = PropertyImage::count();
echo "PropertyImage total rows: " . $imagesCount . PHP_EOL;

$sampleImage = PropertyImage::first();
if ($sampleImage) {
    echo "Sample Image: property_id={$sampleImage->property_id}, path={$sampleImage->image_path}, is_cover={$sampleImage->is_cover}\n";
}

$propsWithImages = Property::has('images')->count();
echo "Properties with images: " . $propsWithImages . PHP_EOL;
