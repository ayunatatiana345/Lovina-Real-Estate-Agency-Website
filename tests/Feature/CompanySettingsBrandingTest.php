<?php

namespace Tests\Feature;

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

    public function test_company_settings_branding_upload_updates_database_and_url()
    {
        Storage::fake('public');
        $settings = CompanySetting::getSettings();

        $primaryLogo = UploadedFile::fake()->image('custom_primary.png', 300, 100);
        $altLogo = UploadedFile::fake()->image('custom_alt.png', 300, 80);
        $favicon = UploadedFile::fake()->image('custom_favicon.png', 64, 64);

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'company_name' => 'PT Lovina North Bali Real Estate Agency',
            'site_title' => 'PT Lovina North Bali Real Estate Agency',
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

        $this->assertStringStartsWith('branding/', $settings->logo_primary);
        $this->assertStringStartsWith('branding/', $settings->logo_alt);
        $this->assertStringStartsWith('branding/', $settings->favicon);

        Storage::disk('public')->assertExists($settings->logo_primary);
        Storage::disk('public')->assertExists($settings->logo_alt);
        Storage::disk('public')->assertExists($settings->favicon);
    }
}
