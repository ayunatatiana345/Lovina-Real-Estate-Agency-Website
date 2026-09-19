<?php

namespace Tests\Feature;

use App\Models\Benefit;
use App\Models\CmsContent;
use App\Models\Location;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageCmsAlignmentTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create([
            'name' => 'Lovina Admin',
            'email' => 'admin@lovinanorthbali.com',
            'password' => bcrypt('password'),
        ]);

        // Create benefits
        Benefit::create([
            'page' => 'homepage',
            'title' => 'North Bali Property Focus',
            'description' => 'Deep knowledge of Lovina, Singaraja, and surrounding regions.',
            'sort_order' => 1,
        ]);
        Benefit::create([
            'page' => 'homepage',
            'title' => 'Tailored Property Search',
            'description' => 'Curated properties matching your lifestyle and investment goals.',
            'sort_order' => 2,
        ]);
        Benefit::create([
            'page' => 'homepage',
            'title' => 'Local Property Support',
            'description' => 'End-to-end guidance from initial viewings to legal closing.',
            'sort_order' => 3,
        ]);

        $cat = \App\Models\PropertyCategory::create([
            'name' => 'Villa',
            'slug' => 'villa',
            'status' => 'active',
        ]);

        $loc = Location::create([
            'name' => 'Lovina Beach',
            'slug' => 'lovina-beach',
            'is_popular' => true,
            'status' => 'active',
        ]);

        Property::create([
            'name' => 'Villa Kalibukbuk Retreat',
            'slug' => 'villa-kalibukbuk-retreat',
            'category_id' => $cat->id,
            'location_id' => $loc->id,
            'price' => 4500000000,
            'is_featured' => true,
            'status' => 'published',
        ]);
    }

    public function test_admin_cms_page_loads_with_homepage_tab()
    {
        $response = $this->actingAs($this->admin)->get('/admin/cms?tab=homepage');
        $response->assertStatus(200);
        $response->assertSee('Why Choose Us');
        $response->assertSee('Property Type Dropdown');
        $response->assertDontSee('Location Dropdown');
        $response->assertSee('Price Range Dropdown');
    }

    public function test_why_choose_us_saves_and_syncs_benefits_table()
    {
        $payload = [
            'section' => 'sec-why-choose',
            'why_heading' => 'Why Invest in North Bali?',
            'why_description' => 'Tested customized description for North Bali investment.',
            'benefit_titles' => [
                'Custom Benefit 1 Title',
                'Custom Benefit 2 Title',
                'Custom Benefit 3 Title',
            ],
            'benefit_descriptions' => [
                'Custom Benefit 1 Description text.',
                'Custom Benefit 2 Description text.',
                'Custom Benefit 3 Description text.',
            ],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/cms/homepage', $payload);
        $response->assertRedirect(route('admin.cms.index', ['tab' => 'homepage', 'section' => 'sec-why-choose']));
        $response->assertSessionHas('success', 'Homepage section saved successfully.');

        // Verify Benefit records in DB
        $benefits = Benefit::where('page', 'homepage')->orderBy('sort_order', 'asc')->get();
        $this->assertCount(3, $benefits);
        $this->assertEquals('Custom Benefit 1 Title', $benefits[0]->title);
        $this->assertEquals('Custom Benefit 1 Description text.', $benefits[0]->description);
        $this->assertEquals('Custom Benefit 2 Title', $benefits[1]->title);
        $this->assertEquals('Custom Benefit 3 Description text.', $benefits[2]->description);

        // Verify public Homepage reflects this
        $publicRes = $this->get('/');
        $publicRes->assertStatus(200);
        $publicRes->assertSee('Why Invest in North Bali?');
        $publicRes->assertSee('Custom Benefit 1 Title');
        $publicRes->assertSee('Custom Benefit 1 Description text.');
    }

    public function test_save_section_isolation()
    {
        // Save search section
        $searchPayload = [
            'section' => 'sec-search',
            'search_filter_type' => '1',
            'search_filter_price' => '1',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/cms/homepage', $searchPayload);
        $response->assertRedirect(route('admin.cms.index', ['tab' => 'homepage', 'section' => 'sec-search']));

        // Verify CMS content for search
        $searchCms = CmsContent::where('page', 'homepage')->where('section_key', 'search')->first();
        $this->assertNotNull($searchCms);
        $this->assertTrue($searchCms->content['filter_type'] ?? false);
        $this->assertFalse($searchCms->content['filter_location'] ?? false);
        $this->assertTrue($searchCms->content['filter_price'] ?? false);

        // Verify why_choose was not modified or overwritten
        $benefits = Benefit::where('page', 'homepage')->get();
        $this->assertCount(3, $benefits);
    }

    public function test_save_all_preserves_featured_properties_and_popular_locations()
    {
        // Ensure we have featured properties and popular locations
        $initialFeaturedIds = Property::where('is_featured', true)->pluck('id')->all();
        $initialPopularIds = Location::where('is_popular', true)->pluck('id')->all();
        sort($initialFeaturedIds);
        sort($initialPopularIds);

        $saveAllPayload = [
            'section' => 'all',
            'hero_heading' => 'Discover North Bali Real Estate Test',
            'hero_subheading' => 'Exclusive villas, land, and beachfront properties in Lovina and surrounding areas.',
            'why_heading' => 'Why Partner With Lovina Real Estate',
            'why_description' => 'Unmatched local expertise and trusted legal guidance.',
            'search_filter_type' => '1',
            'search_filter_price' => '1',
            'featured_ids' => $initialFeaturedIds,
            'popular_location_ids' => $initialPopularIds,
            'cta_heading' => 'Ready to find property?',
            'cta_description' => 'Contact our team',
            'cta_button_text' => 'Contact',
            'benefit_titles' => [
                'North Bali Property Focus',
                'Tailored Property Search',
                'Local Property Support',
            ],
            'benefit_descriptions' => [
                'Deep knowledge of Lovina, Singaraja, and surrounding regions.',
                'Curated properties matching your lifestyle and investment goals.',
                'End-to-end guidance from initial viewings to legal closing.',
            ],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/cms/homepage', $saveAllPayload);
        $response->assertRedirect(route('admin.cms.index', ['tab' => 'homepage', 'section' => 'sec-hero']));
        $response->assertSessionHas('success', 'All homepage settings saved successfully.');

        // Verify featured properties and popular locations remain identical
        $afterFeaturedIds = Property::where('is_featured', true)->pluck('id')->all();
        $afterPopularIds = Location::where('is_popular', true)->pluck('id')->all();
        sort($afterFeaturedIds);
        sort($afterPopularIds);

        $this->assertEquals($initialFeaturedIds, $afterFeaturedIds);
        $this->assertEquals($initialPopularIds, $afterPopularIds);
    }

    public function test_hero_buttons_editor_not_displayed_in_admin_cms()
    {
        $response = $this->actingAs($this->admin)->get('/admin/cms?tab=homepage&section=sec-hero');
        $response->assertStatus(200);
        $response->assertSee('Hero Background Image');
        $response->assertSee('Main Heading');
        $response->assertSee('Description');
        $response->assertSee('Overlay');
        $response->assertSee('Overlay Opacity');
        $response->assertSee('Text Alignment');
        $response->assertDontSee('+ Add Button');
        $response->assertDontSee('Button Label');
    }

    public function test_save_hero_section_updates_hero_without_modifying_other_sections()
    {
        $heroPayload = [
            'section' => 'sec-hero',
            'hero_enabled' => '1',
            'hero_small_title' => 'Exclusive North Bali Properties',
            'hero_heading' => 'Find Your Luxury Sanctuary in Lovina',
            'hero_subheading' => 'Handpicked ocean view villas and investment properties in North Bali.',
            'hero_overlay' => 'dark',
            'hero_overlay_opacity' => '60',
            'hero_text_alignment' => 'left',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/cms/homepage', $heroPayload);
        $response->assertRedirect(route('admin.cms.index', ['tab' => 'homepage', 'section' => 'sec-hero']));
        $response->assertSessionHas('success', 'Homepage section saved successfully.');

        // Verify Hero CMS content in DB
        $heroCms = CmsContent::where('page', 'homepage')->where('section_key', 'hero')->first();
        $this->assertNotNull($heroCms);
        $this->assertEquals('Exclusive North Bali Properties', $heroCms->content['small_title']);
        $this->assertEquals('Find Your Luxury Sanctuary in Lovina', $heroCms->content['heading']);
        $this->assertEquals('Handpicked ocean view villas and investment properties in North Bali.', $heroCms->content['subheading']);

        // Verify public Homepage reflects new hero content and does NOT show a hero button
        $publicRes = $this->get('/');
        $publicRes->assertStatus(200);
        $publicRes->assertSee('Exclusive North Bali Properties');
        $publicRes->assertSee('Find Your Luxury Sanctuary in Lovina');
        $publicRes->assertDontSee('Browse Properties</a>', false);

        // Verify benefits, featured properties, and locations are untouched
        $this->assertCount(3, Benefit::where('page', 'homepage')->get());
        $this->assertEquals(1, Property::where('is_featured', true)->count());
        $this->assertEquals(1, Location::where('is_popular', true)->count());
    }

    public function test_public_search_and_filters_functionality()
    {
        // 1. Search by location keyword
        $resLocation = $this->get('/properties?keyword=Lovina');
        $resLocation->assertStatus(200);
        $resLocation->assertSee('Villa Kalibukbuk Retreat');

        // 2. Search by property name
        $resName = $this->get('/properties?keyword=Kalibukbuk');
        $resName->assertStatus(200);
        $resName->assertSee('Villa Kalibukbuk Retreat');

        // 3. Filter by property type
        $resType = $this->get('/properties?type=villa');
        $resType->assertStatus(200);
        $resType->assertSee('Villa Kalibukbuk Retreat');

        // 4. Filter by price range
        $resPrice = $this->get('/properties?price_range=under_5b');
        $resPrice->assertStatus(200);
        $resPrice->assertSee('Villa Kalibukbuk Retreat');

        // 5. Combined filters
        $resCombined = $this->get('/properties?keyword=Lovina&type=villa&price_range=under_5b');
        $resCombined->assertStatus(200);
        $resCombined->assertSee('Villa Kalibukbuk Retreat');

        // 6. No results behavior
        $resNone = $this->get('/properties?keyword=NonExistentPlot999');
        $resNone->assertStatus(200);
        $resNone->assertSee('No Properties Found');
    }
}
