<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanySettingsBrandingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@lovina.com',
        ]);
    }

    public function test_company_setting_default_branding_accessors_and_views()
    {
        $settings = CompanySetting::getSettings();

        $this->assertNotEmpty($settings->logo_primary_url);
        $this->assertNotEmpty($settings->logo_alt_url);
        $this->assertNotEmpty($settings->favicon_url);

        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertSee('prev-primary-logo-img');
        $response->assertSee('prev-alt-logo-img');
        $response->assertSee('prev-favicon-img');
        $response->assertSee(e($settings->logo_primary_url), false);
        $response->assertSee(e($settings->logo_alt_url), false);
        $response->assertSee(e($settings->favicon_url), false);
    }

    public function test_site_description_is_connected_to_meta_description_and_preserves_page_overrides()
    {
        $settings = CompanySetting::getSettings();
        $settings->update([
            'site_description' => 'Custom Lovina Site Description for Real Estate SEO.',
        ]);

        // Public home page uses the site description
        $homeRes = $this->get(route('home'));
        $homeRes->assertStatus(200);
        $homeRes->assertSee('<meta name="description" content="Custom Lovina Site Description for Real Estate SEO.">', false);

        // Page with specific meta description (e.g. Article) does NOT get overwritten
        $article = Article::create([
            'title' => 'Sample Article Title',
            'slug' => 'sample-article-title',
            'content' => 'Sample content for article.',
            'meta_description' => 'Specific unique article meta description here.',
            'category' => 'market-insights',
            'status' => 'published',
            'published_at' => now(),
            'author_name' => 'John Doe',
        ]);

        $articleRes = $this->get(route('articles.show', $article->slug));
        $articleRes->assertStatus(200);
        $articleRes->assertSee('<meta name="description" content="Specific unique article meta description here.">', false);
        $articleRes->assertDontSee('<meta name="description" content="Custom Lovina Site Description for Real Estate SEO.">', false);
    }

    public function test_new_branding_upload_generates_seo_friendly_filenames_and_actual_webp()
    {
        Storage::fake('public');
        $settings = CompanySetting::getSettings();

        $primaryLogo = UploadedFile::fake()->image('IMG_9381.PNG', 400, 150);
        $altLogo = UploadedFile::fake()->image('DSC_4821.JPG', 350, 100);
        $favicon = UploadedFile::fake()->image('WhatsApp_Favicon.PNG', 64, 64);

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'company_name' => 'PT Lovina North Bali Real Estate Agency',
            'site_title' => 'PT Lovina North Bali Real Estate Agency',
            'site_description' => 'Trusted real estate agency in Lovina Bali.',
            'phone' => '0859 3666 6384',
            'whatsapp' => '0859 3666 6384',
            'email' => 'lovinanorthbaliagency2023@gmail.com',
            'address' => 'Jl. Desa Kalibukbuk-Anturan, Buleleng, Bali',
            'logo_primary' => $primaryLogo,
            'logo_alt' => $altLogo,
            'favicon' => $favicon,
        ]);

        $response->assertRedirect();
        $settings->refresh();

        // 1. Filename SEO conventions:
        // Format: branding/lovina-north-bali-real-estate-logo-xxxx.webp
        $this->assertMatchesRegularExpression(
            '/^branding\/lovina-north-bali-real-estate-logo-[a-f0-9]{4}\.webp$/',
            $settings->logo_primary
        );

        // Format: branding/lovina-north-bali-real-estate-alternative-logo-xxxx.webp
        $this->assertMatchesRegularExpression(
            '/^branding\/lovina-north-bali-real-estate-alternative-logo-[a-f0-9]{4}\.webp$/',
            $settings->logo_alt
        );

        // Format: branding/lovina-north-bali-real-estate-favicon-xxxx.webp
        $this->assertMatchesRegularExpression(
            '/^branding\/lovina-north-bali-real-estate-favicon-[a-f0-9]{4}\.webp$/',
            $settings->favicon
        );

        // 2. Storage persistence
        Storage::disk('public')->assertExists($settings->logo_primary);
        Storage::disk('public')->assertExists($settings->logo_alt);
        Storage::disk('public')->assertExists($settings->favicon);

        // 3. Actual WebP header validation: First 4 bytes RIFF, bytes 8-12 WEBP
        $primaryBytes = Storage::disk('public')->get($settings->logo_primary);
        $this->assertStringStartsWith('RIFF', $primaryBytes);
        $this->assertEquals('WEBP', substr($primaryBytes, 8, 4));

        $altBytes = Storage::disk('public')->get($settings->logo_alt);
        $this->assertStringStartsWith('RIFF', $altBytes);
        $this->assertEquals('WEBP', substr($altBytes, 8, 4));

        $favBytes = Storage::disk('public')->get($settings->favicon);
        $this->assertStringStartsWith('RIFF', $favBytes);
        $this->assertEquals('WEBP', substr($favBytes, 8, 4));

        // 4. Public website layout renders new branding URLs
        $publicRes = $this->get(route('home'));
        $publicRes->assertStatus(200);
        $publicRes->assertSee(e($settings->logo_primary_url), false);
        $publicRes->assertSee(e($settings->logo_alt_url), false);
        $publicRes->assertSee(e($settings->favicon_url), false);
    }
}
