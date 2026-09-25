<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LocationImageUploadTest extends TestCase
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

    public function test_admin_can_upload_location_image_with_seo_naming_and_webp_conversion()
    {
        Storage::fake('public');

        $locLovina = Location::create([
            'name' => 'Lovina',
            'slug' => 'lovina',
            'description' => 'Lovina coastal tourism area.',
            'status' => 'active',
            'is_popular' => true,
        ]);

        $locCeluk = Location::create([
            'name' => 'Celuk Buluh',
            'slug' => 'celuk-buluh',
            'description' => 'Celuk Buluh near Kalibukbuk.',
            'status' => 'active',
            'is_popular' => false,
        ]);

        $locSingSing = Location::create([
            'name' => 'Sing-Sing',
            'slug' => 'sing-sing',
            'description' => 'Sing-Sing valley area.',
            'status' => 'active',
            'is_popular' => false,
        ]);

        // Upload Lovina image
        $image1 = UploadedFile::fake()->image('my_random_photo.png', 800, 600);
        $response1 = $this->actingAs($this->admin)->put(route('admin.locations.update', $locLovina->id), [
            'name' => 'Lovina',
            'description' => 'Updated Lovina description.',
            'status' => 'active',
            'image' => $image1,
        ]);

        $response1->assertRedirect(route('admin.locations.index'));
        $locLovina->refresh();

        $this->assertEquals('locations/lovina-location-01.webp', $locLovina->image);
        Storage::disk('public')->assertExists('locations/lovina-location-01.webp');

        // Upload Celuk Buluh image (space in name)
        $image2 = UploadedFile::fake()->image('IMG_2026.jpg', 1000, 750);
        $response2 = $this->actingAs($this->admin)->put(route('admin.locations.update', $locCeluk->id), [
            'name' => 'Celuk Buluh',
            'description' => 'Updated Celuk Buluh description.',
            'status' => 'active',
            'image' => $image2,
        ]);

        $response2->assertRedirect(route('admin.locations.index'));
        $locCeluk->refresh();

        $this->assertEquals('locations/celuk-buluh-location-01.webp', $locCeluk->image);
        Storage::disk('public')->assertExists('locations/celuk-buluh-location-01.webp');

        // Upload Sing-Sing image (hyphen in name)
        $image3 = UploadedFile::fake()->image('camera_shot.jpeg', 1200, 800);
        $response3 = $this->actingAs($this->admin)->put(route('admin.locations.update', $locSingSing->id), [
            'name' => 'Sing-Sing',
            'description' => 'Updated Sing-Sing description.',
            'status' => 'active',
            'image' => $image3,
        ]);

        $response3->assertRedirect(route('admin.locations.index'));
        $locSingSing->refresh();

        $this->assertEquals('locations/sing-sing-location-01.webp', $locSingSing->image);
        Storage::disk('public')->assertExists('locations/sing-sing-location-01.webp');

        // Public page displays locations
        $indexResponse = $this->get(route('locations.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('lovina-location-01.webp');
        $indexResponse->assertSee('celuk-buluh-location-01.webp');
        $indexResponse->assertSee('sing-sing-location-01.webp');
    }

    public function test_invalid_image_upload_is_rejected_gracefully()
    {
        $loc = Location::create([
            'name' => 'Banjar',
            'slug' => 'banjar',
            'description' => 'Banjar hot springs region.',
            'status' => 'active',
            'is_popular' => false,
        ]);

        $invalidFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->admin)->put(route('admin.locations.update', $loc->id), [
            'name' => 'Banjar',
            'description' => 'Banjar hot springs region.',
            'status' => 'active',
            'image' => $invalidFile,
        ]);

        $response->assertSessionHasErrors('image');
        $loc->refresh();
        $this->assertNull($loc->image);
    }
}
