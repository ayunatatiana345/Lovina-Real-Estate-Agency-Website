<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\Location;

$prop = Property::find(14);
$dencarik = Location::where('slug', 'dencarik')->where('status', 'active')->first();

if ($prop && $dencarik) {
    $oldLocId = $prop->location_id;
    $oldLocName = $prop->location ? $prop->location->name : 'N/A';
    
    $prop->location_id = $dencarik->id;
    $prop->save();
    
    echo "Property ID 14 ('{$prop->name}') updated from Location [{$oldLocId}: {$oldLocName}] to [{$dencarik->id}: {$dencarik->name}].\n";
} else {
    echo "Could not find Property 14 or Dencarik location.\n";
}
