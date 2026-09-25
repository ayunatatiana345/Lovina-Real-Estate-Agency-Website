<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PropertyImage;
use Illuminate\Http\Request;

// Create a simulated request on 127.0.0.1:8000
$request = Request::create('http://127.0.0.1:8000/admin/properties/137/edit', 'GET');
app()->instance('request', $request);

$img = PropertyImage::find(55);

echo "Current image_url with Storage::url():\n";
echo $img->image_url . "\n\n";

echo "With asset('storage/' . path):\n";
echo asset('storage/' . $img->image_path) . "\n\n";
