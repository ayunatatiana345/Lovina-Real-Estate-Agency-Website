<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$s = \App\Models\CompanySetting::first();
echo "Current DB office_photo: " . var_export($s->office_photo, true) . "\n";
echo "Current DB office_photo_url: " . var_export($s->office_photo_url, true) . "\n";
echo "Current DB has_office_photo: " . ($s->has_office_photo ? 'TRUE' : 'FALSE') . "\n";

if (file_exists(public_path('images/office-building.jpg'))) {
    $info = getimagesize(public_path('images/office-building.jpg'));
    echo "images/office-building.jpg exists: " . $info[0] . "x" . $info[1] . " (" . $info['mime'] . ")\n";
}
