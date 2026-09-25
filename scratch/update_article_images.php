<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Article;

$updates = [
    'how-to-choose-the-right-property-in-bali' => 'images/sample-villa-1.jpg',
    'things-to-consider-before-investing-in-bali-property' => 'images/sample-villa-3.jpg',
    'top-areas-in-north-bali-for-villa-investment' => 'images/sample-house-1.jpg',
];

foreach ($updates as $slug => $imgPath) {
    $article = Article::where('slug', $slug)->first();
    if ($article) {
        $article->featured_image = $imgPath;
        $article->save();
        echo "Updated '{$article->title}' with image: {$imgPath} (Image URL: {$article->image_url})\n";
    } else {
        echo "Article with slug '{$slug}' not found!\n";
    }
}
