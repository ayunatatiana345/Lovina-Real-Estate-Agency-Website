<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$properties = App\Models\Property::with('images')->get();
foreach ($properties as $p) {
    $covers = $p->images->where('is_cover', true);
    if ($covers->count() > 1) {
        echo "Property #{$p->id} ({$p->name}) has {$covers->count()} covers: " . $covers->pluck('id')->implode(', ') . "\n";
    }
}
echo "Check completed.\n";
