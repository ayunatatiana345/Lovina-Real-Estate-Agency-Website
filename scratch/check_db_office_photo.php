<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$s = \App\Models\CompanySetting::first();
echo "ID: " . $s->id . "\n";
echo "office_photo: " . var_export($s->office_photo, true) . "\n";
echo "office_photo_url: " . var_export($s->office_photo_url, true) . "\n";
echo "has_office_photo: " . ($s->has_office_photo ? 'TRUE' : 'FALSE') . "\n";
echo "Storage disk exists: " . (\Illuminate\Support\Facades\Storage::disk('public')->exists($s->office_photo ?? '') ? 'YES' : 'NO') . "\n";
echo "public/storage path exists: " . (file_exists(public_path('storage/' . ($s->office_photo ?? ''))) ? 'YES' : 'NO') . "\n";
echo "public path direct exists: " . (file_exists(public_path($s->office_photo ?? '')) ? 'YES' : 'NO') . "\n";
