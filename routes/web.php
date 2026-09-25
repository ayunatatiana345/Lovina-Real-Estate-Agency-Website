<?php

use Illuminate\Support\Facades\Route;

// Public Website Controllers
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PropertyController;
use App\Http\Controllers\Public\LocationController;
use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\InquiryController;
use App\Http\Controllers\Public\ArticleController;
use App\Http\Controllers\Public\CurrencyController;

// Admin Dashboard Controllers
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\WebsiteCmsController as AdminCmsController;
use App\Http\Controllers\Admin\CompanySettingController as AdminSettingController;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Admin\PropertyCategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/currency/{currency}', [CurrencyController::class, 'switchCurrency'])->name('currency.switch');
Route::get('/api/currency/meta', [CurrencyController::class, 'getMeta'])->name('currency.meta');
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{slug}', [PropertyController::class, 'show'])->name('properties.show');
Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
Route::get('/locations/{slug}', [LocationController::class, 'show'])->name('locations.show');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/inquiry/store', [InquiryController::class, 'store'])->name('inquiry.store');

// SEO Helpers
Route::get('/robots.txt', function () {
    $baseUrl = config('app.url');
    if (str_contains($baseUrl, 'localhost') || str_contains($baseUrl, '127.0.0.1')) {
        $baseUrl = 'https://lovinanorthbali.com';
    }
    $sitemapUrl = rtrim($baseUrl, '/') . '/sitemap.xml';
    $content = "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /currency/\nDisallow: /api/\nDisallow: /inquiry/\n\nSitemap: {$sitemapUrl}\n";
    return response($content, 200, ['Content-Type' => 'text/plain']);
});

Route::get('/sitemap.xml', function () {
    $properties = \App\Models\Property::where('status', 'published')->get();
    $locations = \App\Models\Location::where('status', 'active')->get();
    $articles = \App\Models\Article::published()->get();
    
    return response()->view('public.sitemap', compact('properties', 'locations', 'articles'))->header('Content-Type', 'text/xml');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // Auth Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::get('/login-redirect', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Password Reset Routes
    Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPasswordForm'])->name('admin.password.request');
    Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLinkEmail'])->name('admin.password.email');
    Route::get('/reset-password/{token}', [AdminAuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('admin.password.update');

    // Protected Admin Routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Website CMS
        Route::get('/cms', [AdminCmsController::class, 'index'])->name('admin.cms.index');
        Route::post('/cms/homepage', [AdminCmsController::class, 'updateHomepage'])->name('admin.cms.homepage.update');
        Route::post('/cms/about', [AdminCmsController::class, 'updateAbout'])->name('admin.cms.about.update');

        // Company Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('admin.settings.update');

        // News Articles Management
        Route::get('/articles', [AdminArticleController::class, 'index'])->name('admin.articles.index');
        Route::get('/articles/views-counts', [AdminArticleController::class, 'getViewsCounts'])->name('admin.articles.views-counts');
        Route::get('/articles/create', [AdminArticleController::class, 'create'])->name('admin.articles.create');
        Route::post('/articles', [AdminArticleController::class, 'store'])->name('admin.articles.store');
        Route::post('/articles/upload-content-image', [AdminArticleController::class, 'uploadContentImage'])->name('admin.articles.upload-content-image');
        Route::get('/articles/{id}/edit', [AdminArticleController::class, 'edit'])->name('admin.articles.edit');
        Route::put('/articles/{id}', [AdminArticleController::class, 'update'])->name('admin.articles.update');
        Route::delete('/articles/{id}', [AdminArticleController::class, 'destroy'])->name('admin.articles.destroy');

        // Properties & Categories
        Route::get('/properties', [AdminPropertyController::class, 'index'])->name('admin.properties.index');
        Route::get('/properties/create', [AdminPropertyController::class, 'create'])->name('admin.properties.create');
        Route::post('/properties', [AdminPropertyController::class, 'store'])->name('admin.properties.store');
        Route::get('/properties/{id}/edit', [AdminPropertyController::class, 'edit'])->name('admin.properties.edit');
        Route::put('/properties/{id}', [AdminPropertyController::class, 'update'])->name('admin.properties.update');
        Route::delete('/properties/{id}', [AdminPropertyController::class, 'destroy'])->name('admin.properties.destroy');
        Route::post('/properties/{id}/toggle-featured', [AdminPropertyController::class, 'toggleFeatured'])->name('admin.properties.toggle-featured');
        Route::delete('/properties/image/{imageId}', [AdminPropertyController::class, 'deleteImage'])->name('admin.properties.delete-image');
        Route::post('/properties/image/{imageId}/set-cover', [AdminPropertyController::class, 'setCoverImage'])->name('admin.properties.set-cover');
        Route::post('/properties/image/{imageId}/unset-cover', [AdminPropertyController::class, 'unsetCoverImage'])->name('admin.properties.unset-cover');
        Route::post('/properties/{id}/reorder-images', [AdminPropertyController::class, 'reorderImages'])->name('admin.properties.reorder-images');

        Route::post('/properties/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
        Route::put('/properties/categories/{id}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
        Route::post('/properties/categories/{id}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('admin.categories.toggle-status');
        Route::delete('/properties/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

        // Locations
        Route::get('/locations', [AdminLocationController::class, 'index'])->name('admin.locations.index');
        Route::post('/locations', [AdminLocationController::class, 'store'])->name('admin.locations.store');
        Route::put('/locations/{id}', [AdminLocationController::class, 'update'])->name('admin.locations.update');
        Route::delete('/locations/{id}', [AdminLocationController::class, 'destroy'])->name('admin.locations.destroy');
        Route::post('/locations/{id}/toggle-popular', [AdminLocationController::class, 'togglePopular'])->name('admin.locations.toggle-popular');

        // Inquiries
        Route::get('/inquiries', [AdminInquiryController::class, 'index'])->name('admin.inquiries.index');
        Route::get('/inquiries/{id}', [AdminInquiryController::class, 'show'])->name('admin.inquiries.show');
        Route::put('/inquiries/{id}', [AdminInquiryController::class, 'updateStatus'])->name('admin.inquiries.update');
        Route::post('/inquiries/{id}/reply-email', [AdminInquiryController::class, 'replyEmail'])->name('admin.inquiries.reply-email');
        Route::delete('/inquiries/{id}', [AdminInquiryController::class, 'destroy'])->name('admin.inquiries.destroy');
    });
});
