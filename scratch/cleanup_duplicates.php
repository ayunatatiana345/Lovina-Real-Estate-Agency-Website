<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$properties = App\Models\Property::with('images')->get();
foreach ($properties as $p) {
    $covers = $p->images->where('is_cover', true);
    if ($covers->count() > 1) {
        $keeper = $covers->sortByDesc('sort_order')->first();
        echo "Property #{$p->id} ({$p->name}) has {$covers->count()} covers. Keeping ID: {$keeper->id} (sort_order {$keeper->sort_order})\n";
        App\Models\PropertyImage::where('property_id', $p->id)->where('id', '!=', $keeper->id)->update(['is_cover' => false]);
    }
}

echo "\n--- Final Verification ---\n";
$properties = App\Models\Property::with('images')->get();
$hasDuplicates = false;
foreach ($properties as $p) {
    $covers = $p->images->where('is_cover', true);
    if ($covers->count() > 1) {
        $hasDuplicates = true;
        echo "WARNING: Property #{$p->id} still has {$covers->count()} covers!\n";
    }
}
if (!$hasDuplicates) {
    echo "SUCCESS: All properties have at most 1 cover image in database.\n";
}
