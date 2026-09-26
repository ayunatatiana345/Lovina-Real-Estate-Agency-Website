<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CompanySetting;
use App\Models\Property;
use Illuminate\Support\ViewErrorBag;

view()->share('errors', new ViewErrorBag);

$settings = CompanySetting::getSettings();
$properties = Property::where('status', 'published')->get();

$html = view('public.contact', compact('settings', 'properties'))->render();
echo "Contact view rendered successfully: " . strlen($html) . " bytes\n";
echo "Contains 'Our Office': " . (str_contains($html, 'Our Office') ? 'YES' : 'NO') . "\n";
