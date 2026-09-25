<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\User;

$admin = User::first();
$property137 = Property::with(['images', 'category', 'location'])->where('id', 137)->first();

echo "=== TESTING ADMIN EDIT VIEW FOR PROPERTY #137 ===\n";
echo "Property ID: {$property137->id} ({$property137->name})\n";
echo "Images in DB: " . $property137->images->count() . "\n";

try {
    $view = view('admin.properties.edit', [
        'property' => $property137,
        'categories' => \App\Models\PropertyCategory::all(),
        'locations' => \App\Models\Location::all(),
        'errors' => new \Illuminate\Support\ViewErrorBag(),
    ])->render();
    
    echo "Render Status: SUCCESS! View length: " . strlen($view) . " bytes\n";
    
    // Count rendered gallery cards
    preg_match_all('/id="gallery-card-(\d+)"/', $view, $matches);
    $renderedCards = $matches[1] ?? [];
    echo "Rendered gallery cards in HTML: " . count($renderedCards) . " (IDs: " . implode(', ', $renderedCards) . ")\n";

    // Check if Main Cover indicator is rendered
    if (strpos($view, '✓ Main Cover') !== false) {
        echo "Main Cover indicator: PRESENT in rendered HTML\n";
    } else {
        echo "Main Cover indicator: NOT FOUND\n";
    }

    // Check cover badge
    if (strpos($view, '★ Cover') !== false) {
        echo "★ Cover badge: PRESENT in rendered HTML\n";
    } else {
        echo "★ Cover badge: NOT FOUND\n";
    }

    // Check which image has the cover badge visible
    foreach ($property137->images as $img) {
        $expectedDisplay = $img->is_cover ? 'inline-block' : 'none';
        if (strpos($view, "id=\"cover-badge-{$img->id}\" style=\"display: {$expectedDisplay};") !== false) {
            echo " - Image #{$img->id} (is_cover=" . ($img->is_cover ? '1' : '0') . "): Badge display '{$expectedDisplay}' verified correctly.\n";
        }
    }

} catch (\Throwable $e) {
    echo "Render Error: " . $e->getMessage() . "\n";
}
