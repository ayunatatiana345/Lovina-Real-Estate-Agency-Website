<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Storage;

$s = CompanySetting::first();
if ($s && $s->office_photo) {
    echo "Current office_photo in DB: " . $s->office_photo . "\n";
    // Check if it's a test file
    if (str_contains($s->office_photo, 'lovina-north-bali-real-estate-office-')) {
        Storage::disk('public')->delete($s->office_photo);
        @unlink(public_path('storage/' . $s->office_photo));
        echo "Deleted dummy test file.\n";
    }
    $s->update(['office_photo' => null]);
    echo "Reset office_photo to NULL in database.\n";
}

echo "Database office_photo is now: " . var_export(CompanySetting::first()->office_photo, true) . "\n";
