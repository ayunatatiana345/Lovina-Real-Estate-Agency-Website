<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Property;
use App\Models\Location;
use App\Models\PropertyCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CurrencySwitchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        Http::fake([
            'https://api.frankfurter.dev/v2/rate/usd/idr' => Http::response([
                'base' => 'USD',
                'quote' => 'IDR',
                'rate' => 17500,
                'date' => '2026-09-13',
            ], 200),
            'https://api.frankfurter.app/latest*' => Http::response([
                'base' => 'USD',
                'date' => '2026-09-13',
                'rates' => ['IDR' => 17500],
            ], 200),
        ]);
    }

    public function test_can_switch_currency_and_persist_in_session()
    {
        $response = $this->get(route('currency.switch', 'USD'));
        $response->assertSessionHas('user_currency', 'USD');

        $response2 = $this->get(route('currency.switch', 'IDR'));
        $response2->assertSessionHas('user_currency', 'IDR');
    }

    public function test_property_price_renders_in_selected_currency()
    {
        $cat = PropertyCategory::create(['name' => 'Villa', 'slug' => 'villa', 'status' => 'active']);
        $loc = Location::create(['name' => 'Lovina', 'slug' => 'lovina', 'status' => 'active']);

        $property = Property::create([
            'name' => 'Test Ocean Villa',
            'slug' => 'test-ocean-villa',
            'category_id' => $cat->id,
            'location_id' => $loc->id,
            'price' => 7000000000, // 7 Billion IDR (~$400,000 USD at 17,500)
            'ownership_type' => 'Freehold',
            'status' => 'published',
            'is_featured' => true,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'land_size' => 500,
            'building_size' => 250,
        ]);

        // 1. Visit in IDR
        $this->withSession(['user_currency' => 'IDR']);
        $resIdr = $this->get(route('properties.show', $property->slug));
        $resIdr->assertStatus(200);
        $resIdr->assertSee('Rp 7.000.000.000');

        // 2. Visit in USD
        $this->withSession(['user_currency' => 'USD']);
        $resUsd = $this->get(route('properties.show', $property->slug));
        $resUsd->assertStatus(200);
        $resUsd->assertSee('$400,000');

        // 3. Database price remains canonical 7,000,000,000 IDR!
        $this->assertEquals(7000000000, $property->fresh()->price);
        $this->assertEquals('Rp 7.000.000.000', $property->fresh()->formatted_price_admin);
    }

    public function test_price_filtering_works_in_idr_and_usd()
    {
        $cat = PropertyCategory::create(['name' => 'Villa', 'slug' => 'villa', 'status' => 'active']);
        $loc = Location::create(['name' => 'Lovina', 'slug' => 'lovina', 'status' => 'active']);

        $cheapProp = Property::create([
            'name' => 'Budget Cottage',
            'slug' => 'budget-cottage',
            'category_id' => $cat->id,
            'location_id' => $loc->id,
            'price' => 1000000000, // 1 Billion IDR (~$57,143)
            'ownership_type' => 'Freehold',
            'status' => 'published',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'land_size' => 100,
            'building_size' => 50,
        ]);

        $luxuryProp = Property::create([
            'name' => 'Luxury Mansion',
            'slug' => 'luxury-mansion',
            'category_id' => $cat->id,
            'location_id' => $loc->id,
            'price' => 8000000000, // 8 Billion IDR (~$457,143)
            'ownership_type' => 'Freehold',
            'status' => 'published',
            'bedrooms' => 5,
            'bathrooms' => 5,
            'land_size' => 1000,
            'building_size' => 500,
        ]);

        // Filter under_2b in IDR
        $this->withSession(['user_currency' => 'IDR']);
        $res = $this->get(route('properties.index', ['price_range' => 'under_2b']));
        $res->assertStatus(200);
        $res->assertSee('Budget Cottage');
        $res->assertDontSee('Luxury Mansion');

        // Filter above_5b in USD
        $this->withSession(['user_currency' => 'USD']);
        $resUsd = $this->get(route('properties.index', ['price_range' => 'above_5b']));
        $resUsd->assertStatus(200);
        $resUsd->assertSee('Luxury Mansion');
        $resUsd->assertDontSee('Budget Cottage');
    }
}
