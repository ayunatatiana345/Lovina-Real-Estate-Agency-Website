<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;

$props = Property::with(['category', 'categories', 'location', 'images'])->orderBy('id')->get();

echo "Total Properties: " . $props->count() . PHP_EOL;
echo "Published: " . $props->where('status', 'published')->count() . PHP_EOL;
echo "Drafts: " . $props->where('status', 'draft')->count() . PHP_EOL;

$emptyDesc = $props->filter(fn($p) => empty(trim($p->description ?? '')));
echo "Empty Descriptions: " . $emptyDesc->count() . PHP_EOL;
foreach ($emptyDesc as $ed) {
    echo " - ID {$ed->id}: {$ed->name} [status: {$ed->status}]\n";
}

$placeholderDesc = $props->filter(fn($p) => preg_match('/(lorem ipsum|coming soon|placeholder|test description|dummy)/i', $p->description ?? ''));
echo "Placeholder/Dummy Descriptions: " . $placeholderDesc->count() . PHP_EOL;
foreach ($placeholderDesc as $pd) {
    echo " - ID {$pd->id}: {$pd->name}\n";
}

$missingLoc = $props->filter(fn($p) => empty($p->location_id));
echo "Missing Location ID: " . $missingLoc->count() . PHP_EOL;

$missingCat = $props->filter(fn($p) => empty($p->category_id));
echo "Missing Category ID: " . $missingCat->count() . PHP_EOL;

$missingImages = $props->filter(fn($p) => $p->images->isEmpty());
echo "Missing Images: " . $missingImages->count() . PHP_EOL;

$missingCover = $props->filter(fn($p) => $p->images->isNotEmpty() && !$p->images->contains('is_cover', true));
echo "Missing Cover: " . $missingCover->count() . PHP_EOL;
