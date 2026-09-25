<?php

namespace Tests\Feature;

use App\Models\CmsContent;
use App\Models\User;
use App\Models\Location;
use App\Models\PropertyCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AboutUsImageFlowTest extends TestCase
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

        Location::create([
            'name' => 'Lovina Central',
            'slug' => 'lovina-central',
            'status' => 'active',
        ]);

        PropertyCategory::create([
            'name' => 'Villa',
            'slug' => 'villa',
            'status' => 'active',
        ]);
    }

    public function test_can_upload_and_save_banner_image_via_section_save()
    {
        $bannerFile = UploadedFile::fake()->image('banner_hero.jpg', 1920, 600);

        $payload = [
            'section' => 'sec-ab-banner',
            'banner_title' => 'About PT Lovina North Bali',
            'banner_subtitle' => 'Your trusted real estate partner in North Bali.',
            'banner_breadcrumb' => 'Home / About Us',
            'banner_image' => $bannerFile,
            'remove_banner_image' => '0',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.cms.about.update'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $bannerContent = CmsContent::getContent('about_us', 'banner');
        $this->assertNotEmpty($bannerContent['image']);
        $this->assertTrue(Storage::disk('public')->exists($bannerContent['image']));

        // Public page displays banner with image background
        $publicRes = $this->get(route('about'));
        $publicRes->assertStatus(200);
        $publicRes->assertSee($bannerContent['image']);
    }

    public function test_can_upload_and_save_story_image_via_section_save()
    {
        $storyFile = UploadedFile::fake()->image('story_team.png', 800, 600);

        $payload = [
            'section' => 'sec-ab-story',
            'story_label' => 'OUR STORY',
            'story_heading' => 'Our Story in North Bali',
            'story_description' => 'Established in 2023 with excellence.',
            'story_image' => $storyFile,
            'remove_story_image' => '0',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.cms.about.update'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $storyContent = CmsContent::getContent('about_us', 'story');
        $this->assertNotEmpty($storyContent['image']);
        $this->assertTrue(Storage::disk('public')->exists($storyContent['image']));

        // Public page renders story image tag
        $publicRes = $this->get(route('about'));
        $publicRes->assertStatus(200);
        $publicRes->assertSee($storyContent['image']);
    }

    public function test_saved_banner_and_story_images_persist_when_opening_or_refreshing_cms()
    {
        // 1. Setup saved images in DB & storage
        $bannerPath = UploadedFile::fake()->image('banner_init.jpg', 1920, 400)->store('cms', 'public');
        $storyPath = UploadedFile::fake()->image('story_init.jpg', 800, 600)->store('cms', 'public');

        CmsContent::updateOrCreate(
            ['page' => 'about_us', 'section_key' => 'banner'],
            ['content' => ['title' => 'About Us', 'subtitle' => 'Our Subtitle', 'image' => $bannerPath]]
        );

        CmsContent::updateOrCreate(
            ['page' => 'about_us', 'section_key' => 'story'],
            ['content' => ['heading' => 'Our Story', 'description' => 'Story text', 'image' => $storyPath]]
        );

        // 2. Open CMS on About tab
        $cmsResponse = $this->actingAs($this->admin)->get(route('admin.cms.index', ['tab' => 'about']));
        $cmsResponse->assertStatus(200);

        // Saved images must be present in the view HTML
        $cmsResponse->assertSee($bannerPath);
        $cmsResponse->assertSee($storyPath);
    }

    public function test_cancelling_selection_before_save_leaves_database_and_storage_unchanged()
    {
        $bannerPath = UploadedFile::fake()->image('original_banner.jpg', 1920, 400)->store('cms', 'public');

        CmsContent::updateOrCreate(
            ['page' => 'about_us', 'section_key' => 'banner'],
            ['content' => ['title' => 'Original Banner', 'subtitle' => 'Original Subtitle', 'image' => $bannerPath]]
        );

        // Admin does not submit or cancels selection, refreshing the page
        $cmsResponse = $this->actingAs($this->admin)->get(route('admin.cms.index', ['tab' => 'about']));
        $cmsResponse->assertStatus(200);
        $cmsResponse->assertSee($bannerPath);

        // Database remains identical
        $bannerContent = CmsContent::getContent('about_us', 'banner');
        $this->assertEquals($bannerPath, $bannerContent['image']);
        $this->assertTrue(Storage::disk('public')->exists($bannerPath));
    }

    public function test_removing_banner_image_sets_db_null_deletes_file_and_public_about_renders_without_image()
    {
        // 1. Initial saved banner image
        $bannerPath = UploadedFile::fake()->image('banner_to_delete.jpg', 1920, 400)->store('cms', 'public');
        CmsContent::updateOrCreate(
            ['page' => 'about_us', 'section_key' => 'banner'],
            ['content' => ['title' => 'About Us', 'subtitle' => 'Sub', 'image' => $bannerPath]]
        );

        $this->assertTrue(Storage::disk('public')->exists($bannerPath));

        // 2. Save Section with remove_banner_image = 1
        $payload = [
            'section' => 'sec-ab-banner',
            'banner_title' => 'About Us',
            'banner_subtitle' => 'Sub',
            'remove_banner_image' => '1',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.cms.about.update'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'images' => [
                'banner_image' => null,
            ],
        ]);

        // 3. Verify DB has null for image
        $bannerContent = CmsContent::getContent('about_us', 'banner');
        $this->assertNull($bannerContent['image']);

        // 4. Verify file was cleaned up from storage
        $this->assertFalse(Storage::disk('public')->exists($bannerPath));

        // 5. Verify public About Us renders without background image
        $publicRes = $this->get(route('about'));
        $publicRes->assertStatus(200);
        $publicRes->assertDontSee($bannerPath);
    }

    public function test_removing_story_image_sets_db_null_deletes_file_and_public_about_renders_without_image()
    {
        // 1. Initial saved story image
        $storyPath = UploadedFile::fake()->image('story_to_delete.jpg', 800, 600)->store('cms', 'public');
        CmsContent::updateOrCreate(
            ['page' => 'about_us', 'section_key' => 'story'],
            ['content' => ['heading' => 'Our Story', 'description' => 'Desc', 'image' => $storyPath]]
        );

        $this->assertTrue(Storage::disk('public')->exists($storyPath));

        // 2. Save Section with remove_story_image = 1
        $payload = [
            'section' => 'sec-ab-story',
            'story_heading' => 'Our Story',
            'story_description' => 'Desc',
            'remove_story_image' => '1',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.cms.about.update'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'images' => [
                'story_image' => null,
            ],
        ]);

        // 3. Verify DB has null for image
        $storyContent = CmsContent::getContent('about_us', 'story');
        $this->assertNull($storyContent['image']);

        // 4. Verify file was cleaned up from storage
        $this->assertFalse(Storage::disk('public')->exists($storyPath));

        // 5. Verify public About Us renders without story image
        $publicRes = $this->get(route('about'));
        $publicRes->assertStatus(200);
        $publicRes->assertDontSee($storyPath);
    }

    public function test_can_reupload_new_banner_and_story_images_after_removal()
    {
        // 1. Remove both
        CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'banner'], ['content' => ['image' => null]]);
        CmsContent::updateOrCreate(['page' => 'about_us', 'section_key' => 'story'], ['content' => ['image' => null]]);

        // 2. Re-upload new images
        $newBanner = UploadedFile::fake()->image('reupload_banner.jpg', 1920, 500);
        $newStory = UploadedFile::fake()->image('reupload_story.jpg', 800, 600);

        $payload = [
            'section' => 'all',
            'banner_title' => 'About Lovina',
            'banner_image' => $newBanner,
            'story_heading' => 'Our Story Reloaded',
            'story_image' => $newStory,
            'remove_banner_image' => '0',
            'remove_story_image' => '0',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.cms.about.update'), $payload);
        $response->assertStatus(200);

        $bannerContent = CmsContent::getContent('about_us', 'banner');
        $storyContent = CmsContent::getContent('about_us', 'story');

        $this->assertNotNull($bannerContent['image']);
        $this->assertNotNull($storyContent['image']);
        $this->assertTrue(Storage::disk('public')->exists($bannerContent['image']));
        $this->assertTrue(Storage::disk('public')->exists($storyContent['image']));
    }
}
