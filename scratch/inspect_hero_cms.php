<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$heroRow = App\Models\CmsContent::where('page', 'homepage')->where('section_key', 'hero')->first();
if ($heroRow) {
    echo "DB Content:\n";
    print_r($heroRow->content);
} else {
    echo "No hero row found in DB\n";
}

$heroDefault = App\Models\CmsContent::getContent('homepage', 'hero', [
    'enabled' => true,
    'background_image' => 'cms/hero-bg.jpg',
    'small_title' => 'Find Your Dream',
    'heading' => 'Welcome to North Bali Real Estate Agency',
    'subheading' => 'If your dream is to live in beautiful North Bali, we can help that dream come true.',
]);
echo "\ngetContent() result:\n";
print_r($heroDefault);
