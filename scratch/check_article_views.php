<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Article;
use App\Models\ArticleView;

$articles = Article::withCount('views')->get();
echo "TOTAL ARTICLES: " . $articles->count() . "\n";
foreach ($articles as $a) {
    echo "ID: {$a->id} | Slug: {$a->slug} | Status: {$a->status} | Views Count (withCount): {$a->views_count} | Actual in DB: " . ArticleView::where('article_id', $a->id)->count() . "\n";
}
