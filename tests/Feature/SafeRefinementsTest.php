<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\CompanySetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SafeRefinementsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $category;
    protected $location;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@lovinarealestate.com',
            'password' => bcrypt('password'),
        ]);

        $this->category = PropertyCategory::create([
            'name' => 'Villa',
            'slug' => 'villa',
            'status' => 'active',
        ]);

        $this->location = Location::create([
            'name' => 'Lovina',
            'slug' => 'lovina',
            'status' => 'active',
        ]);

        CompanySetting::create([
            'company_name' => 'PT Lovina North Bali Real Estate Agency',
            'company_email' => 'info@lovinanorthbali.com',
            'company_phone' => '+62 812 3456 7890',
            'company_address' => 'Jl. Raya Lovina, Kalibukbuk, Buleleng, Bali',
        ]);
    }

    /**
     * Test that category deletion is blocked if properties are assigned.
     */
    public function test_category_with_properties_cannot_be_deleted(): void
    {
        $this->actingAs($this->admin);

        Property::create([
            'name' => 'Test Villa',
            'slug' => 'test-villa',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'price' => 5000000000,
            'status' => 'published',
            'is_featured' => false,
        ]);

        $this->assertEquals(1, $this->category->properties()->count());

        $response = $this->delete(route('admin.categories.destroy', $this->category->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('property_categories', ['id' => $this->category->id]);
    }

    /**
     * Test that category status can be toggled via admin route.
     */
    public function test_category_status_can_be_toggled(): void
    {
        $this->actingAs($this->admin);

        $initialStatus = $this->category->status;

        $response = $this->post(route('admin.categories.toggle-status', $this->category->id));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->category->refresh();
        $this->assertEquals('inactive', $this->category->status);

        // Toggle back
        $response2 = $this->post(route('admin.categories.toggle-status', $this->category->id));
        $response2->assertRedirect();
        $this->category->refresh();
        $this->assertEquals('active', $this->category->status);
    }

    /**
     * Test that featured property limit (max 6) is strictly enforced.
     */
    public function test_cannot_feature_more_than_six_properties(): void
    {
        $this->actingAs($this->admin);

        // Create 6 featured properties
        for ($i = 1; $i <= 6; $i++) {
            Property::create([
                'name' => "Featured Villa {$i}",
                'slug' => "featured-villa-{$i}",
                'category_id' => $this->category->id,
                'location_id' => $this->location->id,
                'price' => 5000000000,
                'status' => 'published',
                'is_featured' => true,
            ]);
        }

        $this->assertEquals(6, Property::where('is_featured', true)->count());

        // Create 7th non-featured property
        $seventh = Property::create([
            'name' => 'Seventh Villa',
            'slug' => 'seventh-villa',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'price' => 5000000000,
            'status' => 'published',
            'is_featured' => false,
        ]);

        $response = $this->post(route('admin.properties.toggle-featured', $seventh->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');

        $seventh->refresh();
        $this->assertFalse((bool)$seventh->is_featured);
        $this->assertEquals(6, Property::where('is_featured', true)->count());
    }

    /**
     * Test that home page only displays explicitly featured properties.
     */
    public function test_homepage_only_shows_explicitly_featured_properties(): void
    {
        // Create 3 featured and 5 non-featured properties
        for ($i = 1; $i <= 3; $i++) {
            Property::create([
                'name' => "Featured Property {$i}",
                'slug' => "featured-property-{$i}",
                'category_id' => $this->category->id,
                'location_id' => $this->location->id,
                'price' => 5000000000,
                'status' => 'published',
                'is_featured' => true,
            ]);
        }

        for ($i = 1; $i <= 5; $i++) {
            Property::create([
                'name' => "Regular Property {$i}",
                'slug' => "regular-property-{$i}",
                'category_id' => $this->category->id,
                'location_id' => $this->location->id,
                'price' => 3000000000,
                'status' => 'published',
                'is_featured' => false,
            ]);
        }

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        $featured = $response->viewData('featuredProperties');
        $this->assertNotNull($featured);
        $this->assertCount(3, $featured, 'Home page must only show the 3 explicitly featured properties, not 6.');

        foreach ($featured as $prop) {
            $this->assertTrue((bool)$prop->is_featured);
            $this->assertEquals('published', $prop->status);
        }
    }
}
