<?php

namespace Tests\Feature;

use App\Models\Benefit;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageFeaturedPropertiesSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Benefit::create([
            'page' => 'homepage',
            'title' => 'Benefit 1',
            'description' => 'Description 1',
            'sort_order' => 1,
        ]);
    }

    public function test_featured_properties_search_field_and_metadata_rendered_correctly()
    {
        $admin = User::create([
            'name' => 'Lovina Admin',
            'email' => 'admin@lovinanorthbali.com',
            'password' => bcrypt('password'),
        ]);

        $locLovina = Location::create([
            'name' => 'Lovina Beach',
            'slug' => 'lovina-beach',
            'status' => 'active',
        ]);

        $catVilla = PropertyCategory::create([
            'name' => 'Luxury Villa',
            'slug' => 'luxury-villa',
            'status' => 'active',
        ]);

        $property = Property::create([
            'name' => 'Villa Sunset Paradise',
            'slug' => 'villa-sunset-paradise',
            'location_id' => $locLovina->id,
            'category_id' => $catVilla->id,
            'price' => 2500000000,
            'status' => 'published',
            'is_featured' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.cms.index', ['tab' => 'homepage']));

        $response->assertStatus(200);
        $response->assertSee('id="featured-prop-search"', false);
        $response->assertSee('id="featured-prop-search-clear"', false);
        $response->assertSee('id="featured-props-list-container"', false);
        $response->assertSee('data-name="villa sunset paradise"', false);
        $response->assertSee('data-location="lovina beach"', false);
        $response->assertSee('data-category="luxury villa"', false);
        $response->assertSee('Villa Sunset Paradise');
        $response->assertSee('Lovina Beach');
        $response->assertSee('Luxury Villa');
    }

    public function test_saving_featured_properties_persists_selection()
    {
        $admin = User::create([
            'name' => 'Lovina Admin',
            'email' => 'admin@lovinanorthbali.com',
            'password' => bcrypt('password'),
        ]);

        $loc = Location::create([
            'name' => 'Singaraja',
            'slug' => 'singaraja',
            'status' => 'active',
        ]);

        $cat = PropertyCategory::create([
            'name' => 'House',
            'slug' => 'house',
            'status' => 'active',
        ]);

        $props = [];
        for ($i = 1; $i <= 3; $i++) {
            $props[] = Property::create([
                'name' => "Property {$i}",
                'slug' => "property-{$i}",
                'location_id' => $loc->id,
                'category_id' => $cat->id,
                'price' => 1000000000 * $i,
                'status' => 'published',
                'is_featured' => false,
            ]);
        }

        $selectedIds = [$props[0]->id, $props[2]->id];

        $response = $this->actingAs($admin)->post('/admin/cms/homepage', [
            'section' => 'sec-featured',
            'featured_title' => 'Handpicked North Bali Properties',
            'featured_ids' => $selectedIds,
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('properties', [
            'id' => $props[0]->id,
            'is_featured' => true,
        ]);
        $this->assertDatabaseHas('properties', [
            'id' => $props[1]->id,
            'is_featured' => false,
        ]);
        $this->assertDatabaseHas('properties', [
            'id' => $props[2]->id,
            'is_featured' => true,
        ]);
    }
}
