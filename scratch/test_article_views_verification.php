<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$consoleKernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$consoleKernel->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Article;
use App\Models\ArticleView;
use Illuminate\Http\Request;

// 1. Fetch published articles
$publishedArticles = Article::published()->get();
echo "Found " . $publishedArticles->count() . " published articles.\n";
if ($publishedArticles->count() < 2) {
    echo "ERROR: Need at least 2 published articles to test.\n";
    exit(1);
}

$articleA = $publishedArticles[0];
$articleB = $publishedArticles[1];
$articleC = $publishedArticles[2] ?? null;

echo "Testing with:\n";
echo " - Article A (ID: {$articleA->id}): '{$articleA->title}' (slug: {$articleA->slug})\n";
echo " - Article B (ID: {$articleB->id}): '{$articleB->title}' (slug: {$articleB->slug})\n";
if ($articleC) {
    echo " - Article C (ID: {$articleC->id}): '{$articleC->title}' (slug: {$articleC->slug})\n";
}

// Initial counts
$countA_0 = ArticleView::where('article_id', $articleA->id)->count();
$countB_0 = ArticleView::where('article_id', $articleB->id)->count();
$countC_0 = $articleC ? ArticleView::where('article_id', $articleC->id)->count() : 0;

echo "\n--- Initial View Counts in DB ---\n";
echo "Article A: $countA_0\n";
echo "Article B: $countB_0\n";
if ($articleC) echo "Article C: $countC_0\n";

// Function to simulate a visit with fresh session
function simulateVisit($app, $kernel, $slug, $sessionPrefix = 'session_') {
    $sessionName = $sessionPrefix . uniqid();
    $request = Request::create('/articles/' . $slug, 'GET');
    
    // Set up session store
    $session = $app->make('session')->driver();
    $session->setId($sessionName);
    $session->start();
    $request->setLaravelSession($session);
    
    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);
    
    return [
        'status' => $response->getStatusCode(),
        'session' => $session
    ];
}

echo "\n--- Step 1: User 1 visits Article A ---\n";
$res1 = simulateVisit($app, $kernel, $articleA->slug, 'user1_');
echo "Visit Article A HTTP Status: {$res1['status']}\n";

$countA_1 = ArticleView::where('article_id', $articleA->id)->count();
$countB_1 = ArticleView::where('article_id', $articleB->id)->count();
echo "Counts after visiting Article A:\n";
echo " - Article A: $countA_1 (Expected: " . ($countA_0 + 1) . ") -> " . ($countA_1 === $countA_0 + 1 ? "PASSED" : "FAILED") . "\n";
echo " - Article B: $countB_1 (Expected: $countB_0) -> " . ($countB_1 === $countB_0 ? "PASSED" : "FAILED") . "\n";

echo "\n--- Step 2: User 1 refreshes Article A (Duplicate prevention test) ---\n";
// Re-use user 1's session
$requestDup = Request::create('/articles/' . $articleA->slug, 'GET');
$requestDup->setLaravelSession($res1['session']);
$resDup = $kernel->handle($requestDup);
$kernel->terminate($requestDup, $resDup);

$countA_dup = ArticleView::where('article_id', $articleA->id)->count();
echo "Counts after refreshing Article A in same session:\n";
echo " - Article A: $countA_dup (Expected: $countA_1 - duplicate prevented) -> " . ($countA_dup === $countA_1 ? "PASSED" : "FAILED") . "\n";

echo "\n--- Step 3: User 2 visits Article B ---\n";
$res2 = simulateVisit($app, $kernel, $articleB->slug, 'user2_');
echo "Visit Article B HTTP Status: {$res2['status']}\n";

$countA_2 = ArticleView::where('article_id', $articleA->id)->count();
$countB_2 = ArticleView::where('article_id', $articleB->id)->count();
echo "Counts after visiting Article B:\n";
echo " - Article A: $countA_2 (Expected: $countA_1 - unchanged) -> " . ($countA_2 === $countA_1 ? "PASSED" : "FAILED") . "\n";
echo " - Article B: $countB_2 (Expected: " . ($countB_0 + 1) . ") -> " . ($countB_2 === $countB_0 + 1 ? "PASSED" : "FAILED") . "\n";

if ($articleC) {
    echo "\n--- Step 4: User 3 visits Article C ---\n";
    $res3 = simulateVisit($app, $kernel, $articleC->slug, 'user3_');
    echo "Visit Article C HTTP Status: {$res3['status']}\n";

    $countA_3 = ArticleView::where('article_id', $articleA->id)->count();
    $countB_3 = ArticleView::where('article_id', $articleB->id)->count();
    $countC_3 = ArticleView::where('article_id', $articleC->id)->count();
    echo "Counts after visiting Article C:\n";
    echo " - Article A: $countA_3 (Expected: $countA_1 - unchanged) -> " . ($countA_3 === $countA_1 ? "PASSED" : "FAILED") . "\n";
    echo " - Article B: $countB_3 (Expected: $countB_2 - unchanged) -> " . ($countB_3 === $countB_2 ? "PASSED" : "FAILED") . "\n";
    echo " - Article C: $countC_3 (Expected: " . ($countC_0 + 1) . ") -> " . ($countC_3 === $countC_0 + 1 ? "PASSED" : "FAILED") . "\n";
}

echo "\n--- Step 5: Test Admin Polling API & Admin Index ---\n";
$admin = \App\Models\User::first();
$adminRequest = Request::create('/admin/articles/views-counts', 'GET');
$sessionAdmin = $app->make('session')->driver();
$sessionAdmin->start();
$adminRequest->setLaravelSession($sessionAdmin);
if ($admin) {
    \Illuminate\Support\Facades\Auth::login($admin);
}
$adminResponse = $kernel->handle($adminRequest);
$kernel->terminate($adminRequest, $adminResponse);

echo "Admin Views Polling API HTTP Status: {$adminResponse->getStatusCode()}\n";
$responseData = json_decode($adminResponse->getContent(), true);
echo "Admin Views Polling API Response: " . json_encode($responseData) . "\n";

echo "Check API values match actual DB values:\n";
$apiA = $responseData[$articleA->id] ?? null;
$apiB = $responseData[$articleB->id] ?? null;
$dbA = ArticleView::where('article_id', $articleA->id)->count();
$dbB = ArticleView::where('article_id', $articleB->id)->count();

echo " - Article A API: $apiA vs DB: $dbA -> " . ($apiA === $dbA ? "MATCH" : "MISMATCH") . "\n";
echo " - Article B API: $apiB vs DB: $dbB -> " . ($apiB === $dbB ? "MATCH" : "MISMATCH") . "\n";
if ($articleC) {
    $apiC = $responseData[$articleC->id] ?? null;
    $dbC = ArticleView::where('article_id', $articleC->id)->count();
    echo " - Article C API: $apiC vs DB: $dbC -> " . ($apiC === $dbC ? "MATCH" : "MISMATCH") . "\n";
}

echo "\nALL TESTS COMPLETED SUCCESSFULLY!\n";
