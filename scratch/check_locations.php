<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (\App\Models\Location::all() as $l) {
    echo "ID: {$l->id} | Name: {$l->name} | Slug: {$l->slug} | Status: {$l->status} | Popular: {$l->is_popular} | Image: {$l->image} | HasImage: " . ($l->has_image ? 'YES' : 'NO') . " | ImageURL: {$l->image_url}\n";
}
