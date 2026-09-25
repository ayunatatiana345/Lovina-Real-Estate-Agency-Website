<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyCoverSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $property;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::create([
            'name' => 'Lovina Admin',
            'email' => 'admin@lovinanorthbali.com',
            'password' => bcrypt('password'),
        ]);

        $location = Location::create([
            'name' => 'Lovina Central',
            'slug' => 'lovina-central',
            'status' => 'active',
        ]);

        $category = PropertyCategory::create([
            'name' => 'Villa',
            'slug' => 'villa',
            'status' => 'active',
        ]);

        $this->property = Property::create([
            'name' => 'Beachfront Luxury Villa',
            'slug' => 'beachfront-luxury-villa',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'price' => 5000000000,
            'ownership_type' => 'Freehold',
            'status' => 'published',
        ]);
    }

    public function test_can_select_photo_a_then_b_then_c_as_cover_via_ajax_endpoint()
    {
        $imgA = PropertyImage::create([
            'property_id' => $this->property->id,
            'image_path' => 'properties/villa-a.webp',
            'is_cover' => true,
            'sort_order' => 1,
        ]);
        $imgB = PropertyImage::create([
            'property_id' => $this->property->id,
            'image_path' => 'properties/villa-b.webp',
            'is_cover' => false,
            'sort_order' => 2,
        ]);
        $imgC = PropertyImage::create([
            'property_id' => $this->property->id,
            'image_path' => 'properties/villa-c.webp',
            'is_cover' => false,
            'sort_order' => 3,
        ]);

        // TEST 1: Photo A is currently cover
        $this->assertTrue((bool)$imgA->fresh()->is_cover);
        $this->assertFalse((bool)$imgB->fresh()->is_cover);
        $this->assertFalse((bool)$imgC->fresh()->is_cover);
        $this->assertEquals(1, PropertyImage::where('property_id', $this->property->id)->where('is_cover', true)->count());

        // TEST 2: Select Photo B as Cover
        $resB = $this->actingAs($this->admin)->post("/admin/properties/image/{$imgB->id}/set-cover");
        $resB->assertStatus(200)->assertJson(['success' => true]);

        $this->assertFalse((bool)$imgA->fresh()->is_cover);
        $this->assertTrue((bool)$imgB->fresh()->is_cover);
        $this->assertFalse((bool)$imgC->fresh()->is_cover);
        $this->assertEquals(1, PropertyImage::where('property_id', $this->property->id)->where('is_cover', true)->count());

        // TEST 3: Select Photo C as Cover
        $resC = $this->actingAs($this->admin)->post("/admin/properties/image/{$imgC->id}/set-cover");
        $resC->assertStatus(200)->assertJson(['success' => true]);

        $this->assertFalse((bool)$imgA->fresh()->is_cover);
        $this->assertFalse((bool)$imgB->fresh()->is_cover);
        $this->assertTrue((bool)$imgC->fresh()->is_cover);
        $this->assertEquals(1, PropertyImage::where('property_id', $this->property->id)->where('is_cover', true)->count());

        // Switch back to Photo A as Cover
        $resA = $this->actingAs($this->admin)->post("/admin/properties/image/{$imgA->id}/set-cover");
        $resA->assertStatus(200)->assertJson(['success' => true]);

        $this->assertTrue((bool)$imgA->fresh()->is_cover);
        $this->assertFalse((bool)$imgB->fresh()->is_cover);
        $this->assertFalse((bool)$imgC->fresh()->is_cover);
        $this->assertEquals(1, PropertyImage::where('property_id', $this->property->id)->where('is_cover', true)->count());
    }

    public function test_can_unset_cover_image_via_ajax_endpoint()
    {
        $imgA = PropertyImage::create([
            'property_id' => $this->property->id,
            'image_path' => 'properties/villa-a.webp',
            'is_cover' => true,
            'sort_order' => 1,
        ]);
        $imgB = PropertyImage::create([
            'property_id' => $this->property->id,
            'image_path' => 'properties/villa-b.webp',
            'is_cover' => false,
            'sort_order' => 2,
        ]);

        // TEST 4: Unset cover
        $response = $this->actingAs($this->admin)->post("/admin/properties/image/{$imgA->id}/unset-cover");
        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertFalse((bool)$imgA->fresh()->is_cover);
        $this->assertFalse((bool)$imgB->fresh()->is_cover);
        $this->assertEquals(0, PropertyImage::where('property_id', $this->property->id)->where('is_cover', true)->count());
    }

    public function test_updating_property_with_empty_existing_cover_id_unsets_cover()
    {
        $imgA = PropertyImage::create([
            'property_id' => $this->property->id,
            'image_path' => 'properties/villa-a.webp',
            'is_cover' => true,
            'sort_order' => 1,
        ]);

        $payload = [
            'name' => 'Beachfront Luxury Villa',
            'slug' => 'beachfront-luxury-villa',
            'category_id' => $this->property->category_id,
            'location_id' => $this->property->location_id,
            'price' => 5000000000,
            'ownership_type' => 'Freehold',
            'status' => 'published',
            'existing_cover_id' => '',
        ];

        $response = $this->actingAs($this->admin)->put("/admin/properties/{$this->property->id}", $payload);
        $response->assertRedirect();

        $this->assertFalse((bool)$imgA->fresh()->is_cover);
        $this->assertEquals(0, PropertyImage::where('property_id', $this->property->id)->where('is_cover', true)->count());
    }

    public function test_updating_property_with_existing_cover_id_persists_selection()
    {
        $imgA = PropertyImage::create([
            'property_id' => $this->property->id,
            'image_path' => 'properties/villa-a.webp',
            'is_cover' => true,
            'sort_order' => 1,
        ]);
        $imgB = PropertyImage::create([
            'property_id' => $this->property->id,
            'image_path' => 'properties/villa-b.webp',
            'is_cover' => false,
            'sort_order' => 2,
        ]);

        $payload = [
            'name' => 'Beachfront Luxury Villa',
            'slug' => 'beachfront-luxury-villa',
            'category_id' => $this->property->category_id,
            'location_id' => $this->property->location_id,
            'price' => 5000000000,
            'ownership_type' => 'Freehold',
            'status' => 'published',
            'existing_cover_id' => $imgB->id,
        ];

        $response = $this->actingAs($this->admin)->put("/admin/properties/{$this->property->id}", $payload);
        $response->assertRedirect();

        $this->assertFalse((bool)$imgA->fresh()->is_cover);
        $this->assertTrue((bool)$imgB->fresh()->is_cover);
        $this->assertEquals(1, PropertyImage::where('property_id', $this->property->id)->where('is_cover', true)->count());
    }

    public function test_uploading_new_photo_does_not_replace_existing_cover_unless_specified()
    {
        $imgA = PropertyImage::create([
            'property_id' => $this->property->id,
            'image_path' => 'properties/villa-a.webp',
            'is_cover' => true,
            'sort_order' => 1,
        ]);

        $newFile = UploadedFile::fake()->image('new-photo.jpg', 800, 600);

        $payload = [
            'name' => 'Beachfront Luxury Villa',
            'slug' => 'beachfront-luxury-villa',
            'category_id' => $this->property->category_id,
            'location_id' => $this->property->location_id,
            'price' => 5000000000,
            'ownership_type' => 'Freehold',
            'status' => 'published',
            'existing_cover_id' => $imgA->id,
            'images' => [$newFile],
        ];

        $response = $this->actingAs($this->admin)->put("/admin/properties/{$this->property->id}", $payload);
        $response->assertRedirect();

        // Image A must still remain the cover
        $this->assertTrue((bool)$imgA->fresh()->is_cover);

        // Newly uploaded image must NOT be cover
        $newUploadedImg = PropertyImage::where('property_id', $this->property->id)->where('id', '!=', $imgA->id)->first();
        $this->assertNotNull($newUploadedImg);
        $this->assertFalse((bool)$newUploadedImg->is_cover);
        $this->assertEquals(1, PropertyImage::where('property_id', $this->property->id)->where('is_cover', true)->count());
    }

    public function test_fallback_works_for_display_without_overwriting_database()
    {
        $img1 = PropertyImage::create([
            'property_id' => $this->property->id,
            'image_path' => 'properties/villa-1.webp',
            'is_cover' => false,
            'sort_order' => 1,
        ]);
        $img2 = PropertyImage::create([
            'property_id' => $this->property->id,
            'image_path' => 'properties/villa-2.webp',
            'is_cover' => false,
            'sort_order' => 2,
        ]);

        // Neither image is marked as cover in DB
        $this->assertFalse((bool)$img1->fresh()->is_cover);
        $this->assertFalse((bool)$img2->fresh()->is_cover);

        // Runtime accessor falls back to first image for display
        $primaryUrl = $this->property->fresh()->primary_image_url;
        $this->assertNotNull($primaryUrl);

        // Database must remain unchanged
        $this->assertFalse((bool)$img1->fresh()->is_cover);
        $this->assertFalse((bool)$img2->fresh()->is_cover);
        $this->assertEquals(0, PropertyImage::where('property_id', $this->property->id)->where('is_cover', true)->count());
    }

    public function test_public_views_reflect_selected_cover()
    {
        $imgA = PropertyImage::create([
            'property_id' => $this->property->id,
            'image_path' => 'properties/villa-a.webp',
            'is_cover' => false,
            'sort_order' => 1,
        ]);
        $imgB = PropertyImage::create([
            'property_id' => $this->property->id,
            'image_path' => 'properties/villa-b.webp',
            'is_cover' => true,
            'sort_order' => 2,
        ]);

        $publicShow = $this->get(route('properties.show', $this->property->slug));
        $publicShow->assertStatus(200);

        // Verify that the cover image in relationship is imgB
        $this->assertEquals($imgB->id, $this->property->coverImage->id);
    }
}
