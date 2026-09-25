<?php

namespace Tests\Feature;

use App\Models\CmsContent;
use App\Models\User;
use App\Services\HeroImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeroBackgroundImageTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::create([
            'name' => 'Lovina Admin',
            'email' => 'admin@lovinanorthbali.com',
            'password' => bcrypt('password'),
        ]);

        \App\Models\Location::create([
            'name' => 'Lovina Central',
            'slug' => 'lovina-central',
            'status' => 'active',
        ]);

        \App\Models\PropertyCategory::create([
            'name' => 'Villa',
            'slug' => 'villa',
            'status' => 'active',
        ]);
    }

    public function test_hero_image_service_generates_seo_friendly_webp_filename()
    {
        $service = new HeroImageService();
        $file = UploadedFile::fake()->image('IMG_8392.jpg', 1920, 800);

        $savedPath = $service->processAndStore($file);

        $this->assertNotNull($savedPath);
        $this->assertStringStartsWith('cms/north-bali-real-estate-hero-', $savedPath);
        $this->assertStringEndsWith('.webp', $savedPath);

        // Verify physical file exists on disk
        $this->assertTrue(Storage::disk('public')->exists($savedPath));

        // Verify the file is genuinely a WebP encoded file
        $diskPath = Storage::disk('public')->path($savedPath);
        $imageInfo = @getimagesize($diskPath);
        $this->assertNotFalse($imageInfo);
        $this->assertEquals('image/webp', $imageInfo['mime']);
    }

    public function test_can_upload_new_hero_background_image_via_admin_cms()
    {
        $file = UploadedFile::fake()->image('camera_photo.png', 1600, 900);

        $payload = [
            'section' => 'sec-hero',
            'hero_enabled' => '1',
            'hero_small_title' => 'Find Your Dream Villa',
            'hero_heading' => 'Welcome to Lovina Real Estate',
            'hero_subheading' => 'Discover luxury beachfront properties.',
            'hero_bg' => $file,
            'remove_hero_bg' => '0',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.cms.homepage.update'), $payload);
        $response->assertRedirect();

        // Check database CMS content
        $heroContent = CmsContent::getContent('homepage', 'hero');
        $this->assertNotEmpty($heroContent['background_image']);
        $this->assertStringStartsWith('cms/north-bali-real-estate-hero-', $heroContent['background_image']);
        $this->assertStringEndsWith('.webp', $heroContent['background_image']);

        // Check public storage
        $this->assertTrue(Storage::disk('public')->exists($heroContent['background_image']));

        // Check public website renders the background image
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee($heroContent['background_image']);
    }

    public function test_can_remove_hero_background_image_and_return_to_plain_state()
    {
        // 1. Upload initial hero image
        $service = new HeroImageService();
        $file = UploadedFile::fake()->image('old_hero.jpg', 1920, 800);
        $initialPath = $service->processAndStore($file);

        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'hero'], [
            'content' => [
                'enabled' => true,
                'heading' => 'Discover Premier Luxury Real Estate',
                'subheading' => 'Explore beachfront villas in North Bali.',
                'background_image' => $initialPath,
            ]
        ]);

        $this->assertTrue(Storage::disk('public')->exists($initialPath));

        // 2. Submit removal request (Remove Image -> Save Section)
        $payload = [
            'section' => 'sec-hero',
            'hero_enabled' => '1',
            'hero_heading' => 'Discover Premier Luxury Real Estate',
            'hero_subheading' => 'Explore beachfront villas in North Bali.',
            'remove_hero_bg' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.cms.homepage.update'), $payload);
        $response->assertRedirect();

        // 3. Verify database Hero image reference is cleared (null)
        $heroContent = CmsContent::getContent('homepage', 'hero');
        $this->assertNull($heroContent['background_image']);

        // 4. Verify old physical file was cleaned up
        $this->assertFalse(Storage::disk('public')->exists($initialPath));

        // 5. Verify public homepage returns to default plain background
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('bg-light-blue');

        // 6. Verify Admin CMS edit page loads in removed/empty state
        $adminResponse = $this->actingAs($this->admin)->get(route('admin.cms.index', ['tab' => 'homepage']));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('No image uploaded');
    }

    public function test_can_upload_new_image_after_removal()
    {
        // Set hero with null background
        CmsContent::updateOrCreate(['page' => 'homepage', 'section_key' => 'hero'], [
            'content' => [
                'enabled' => true,
                'heading' => 'Discover Premier Luxury Real Estate',
                'subheading' => 'Explore beachfront villas in North Bali.',
                'background_image' => null,
            ]
        ]);

        // Upload new image
        $newFile = UploadedFile::fake()->image('new_hero_summer.jpg', 1920, 800);
        $payload = [
            'section' => 'sec-hero',
            'hero_enabled' => '1',
            'hero_heading' => 'Discover Premier Luxury Real Estate',
            'hero_subheading' => 'Explore beachfront villas in North Bali.',
            'hero_bg' => $newFile,
            'remove_hero_bg' => '0',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.cms.homepage.update'), $payload);
        $response->assertRedirect();

        $heroContent = CmsContent::getContent('homepage', 'hero');
        $this->assertNotNull($heroContent['background_image']);
        $this->assertStringStartsWith('cms/north-bali-real-estate-hero-', $heroContent['background_image']);
        $this->assertTrue(Storage::disk('public')->exists($heroContent['background_image']));

        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee($heroContent['background_image']);
    }

    public function test_admin_cms_about_tab_and_homepage_tab_load_successfully()
    {
        // 1. Homepage tab
        $resHome = $this->actingAs($this->admin)->get(route('admin.cms.index', ['tab' => 'homepage']));
        $resHome->assertStatus(200);
        $resHome->assertSee('Website CMS');
        $resHome->assertSee('Hero Section');

        // 2. About Us tab
        $resAbout = $this->actingAs($this->admin)->get(route('admin.cms.index', ['tab' => 'about']));
        $resAbout->assertStatus(200);
        $resAbout->assertSee('Website CMS');
        $resAbout->assertSee('Page Banner');
    }
}
