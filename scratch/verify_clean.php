<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;

$p = Property::find(1);
echo "Property ID 1 description:\n";
echo $p->description . "\n";
if (strpos($p->description, 'TEST_SYNC_CHECK') !== false) {
    echo "\nWARNING: Marker found, removing it now...\n";
    $cleaned = preg_replace('/\n*\[TEST_SYNC_CHECK_.*?\]/s', '', $p->description);
    $p->description = trim($cleaned);
    $p->save();
    echo "Cleaned description:\n" . $p->description . "\n";
} else {
    echo "\nCLEAN: No test marker found.\n";
}
