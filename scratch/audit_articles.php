<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Article;

echo "=== ARTICLES AUDIT ===\n\n";

$articles = Article::all();
echo "Total Articles in DB: " . $articles->count() . "\n\n";

foreach ($articles as $art) {
    echo "ID: {$art->id}\n";
    echo "Title: {$art->title}\n";
    echo "Slug: {$art->slug}\n";
    echo "Category: {$art->category}\n";
    echo "Status: {$art->status}\n";
    echo "Featured: " . ($art->is_featured ? 'YES' : 'NO') . "\n";
    echo "Summary: " . mb_substr($art->summary ?? '', 0, 80) . "...\n";
    echo "Image: {$art->image_path}\n";
    echo "Content Length: " . strlen($art->content ?? '') . " chars\n";
    echo "Meta Title: {$art->meta_title}\n";
    echo "Meta Desc: " . mb_substr($art->meta_description ?? '', 0, 60) . "...\n";
    echo "----------------------------------------\n";
}
