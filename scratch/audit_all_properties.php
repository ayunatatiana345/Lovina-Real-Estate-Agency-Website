<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;

$properties = Property::with(['category', 'categories', 'location', 'images'])->orderBy('id')->get();

$totalCount = $properties->count();
$publishedCount = $properties->where('status', 'published')->count();
$draftCount = $properties->where('status', 'draft')->count();

$issues = [];
$emptyDescriptions = [];
$placeholderDescriptions = [];
$missingLocations = [];
$missingCategories = [];
$noImages = [];
$noCoverImage = [];

foreach ($properties as $p) {
    $desc = trim($p->description ?? '');
    
    if (empty($desc)) {
        $emptyDescriptions[] = [
            'id' => $p->id,
            'name' => $p->name,
            'status' => $p->status,
        ];
    } else {
        // Check for placeholder/dummy patterns
        if (preg_match('/(lorem ipsum|coming soon|placeholder|test description|dummy)/i', $desc)) {
            $placeholderDescriptions[] = [
                'id' => $p->id,
                'name' => $p->name,
                'desc_preview' => substr($desc, 0, 80),
            ];
        }
    }

    if (!$p->category_id && $p->categories->isEmpty()) {
        $missingCategories[] = ['id' => $p->id, 'name' => $p->name];
    }

    if (!$p->location_id) {
        $missingLocations[] = ['id' => $p->id, 'name' => $p->name];
    }

    if ($p->images->isEmpty()) {
        $noImages[] = ['id' => $p->id, 'name' => $p->name];
    } else {
        $hasCover = $p->images->contains('is_cover', true);
        if (!$hasCover) {
            $noCoverImage[] = ['id' => $p->id, 'name' => $p->name];
        }
    }
}

$report = [
    'total_properties' => $totalCount,
    'published_properties' => $publishedCount,
    'draft_properties' => $draftCount,
    'empty_descriptions' => $emptyDescriptions,
    'placeholder_descriptions' => $placeholderDescriptions,
    'missing_categories' => $missingCategories,
    'missing_locations' => $missingLocations,
    'no_images' => $noImages,
    'no_cover_image' => $noCoverImage,
];

echo json_encode($report, JSON_PRETTY_PRINT);
