<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;

$props = Property::with('images')->get();

foreach ($props as $p) {
    if ($p->images->count() > 0) {
        $covers = $p->images->where('is_cover', true);
        echo "Property #{$p->id} ({$p->name}) - Total Images: {$p->images->count()} - Covers count: {$covers->count()}\n";
        foreach ($covers as $c) {
            echo "   -> Cover Image ID: #{$c->id} (sort_order: {$c->sort_order}, path: {$c->image_path})\n";
        }
    }
}
