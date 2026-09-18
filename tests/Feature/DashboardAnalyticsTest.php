<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\CompanySetting;
use App\Models\Inquiry;
use App\Models\Article;
use App\Models\ArticleView;
use App\Models\PageView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected PropertyCategory $category;
    protected Location $location;
    protected Property $property;
    protected Article $article;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Lovina Agency Admin',
            'email' => 'admin@lovinanorthbali.com',
            'password' => Hash::make('password'),
        ]);

        $this->category = PropertyCategory::create([
            'name' => 'Villa',
            'slug' => 'villa',
            'status' => 'active',
        ]);

        $this->location = Location::create([
            'name' => 'Lovina',
            'slug' => 'lovina',
            'description' => 'Beautiful Lovina area',
            'is_popular' => true,
            'status' => 'active',
        ]);

        $this->property = Property::create([
            'name' => 'Azure Vista Residence',
            'slug' => 'azure-vista-residence',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'price' => 5000000000,
            'status' => 'published',
            'is_featured' => true,
            'views_count' => 15,
        ]);

        $this->article = Article::create([
            'title' => 'Living in Lovina',
            'slug' => 'living-in-lovina',
            'category' => 'Location Guide',
            'excerpt' => 'Guide to Lovina',
            'content' => 'Sample article content about Lovina living.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        CompanySetting::create([
            'company_name' => 'PT Lovina North Bali Real Estate Agency',
            'company_email' => 'info@lovinanorthbali.com',
            'company_phone' => '+62 812 3456 7890',
            'company_address' => 'Jl. Raya Lovina, Kalibukbuk, Buleleng, Bali',
        ]);

        Inquiry::create([
            'customer_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+62812345678',
            'subject' => 'Interested in Azure Vista',
            'message' => 'Please send brochure',
            'status' => 'new',
            'property_id' => $this->property->id,
        ]);
    }

    /**
     * Test that dashboard metrics are completely database-driven and not hardcoded.
     */
    public function test_dashboard_renders_with_real_database_metrics(): void
    {
        // Pre-populate 3 page views with 2 distinct sessions
        PageView::create([
            'url' => '/',
            'session_id' => 'session-alpha',
            'created_at' => now()->subDays(2),
        ]);
        PageView::create([
            'url' => '/about',
            'session_id' => 'session-alpha',
            'created_at' => now()->subDays(1),
        ]);
        PageView::create([
            'url' => '/properties',
            'session_id' => 'session-beta',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Verify Total Properties & status counts
        $response->assertViewHas('totalProperties', 1);
        $response->assertViewHas('publishedProperties', 1);
        $response->assertViewHas('draftProperties', 0);

        // Verify Inquiries & status reconciliation
        $response->assertViewHas('totalInquiries', 1);
        $response->assertViewHas('newInquiries', 1);
        $response->assertViewHas('readInquiries', 0);
        $response->assertViewHas('repliedInquiries', 0);

        // Verify Page Views and Unique Visitors are REAL (not simulated 1256!)
        $response->assertViewHas('totalViews', 3);
        $response->assertViewHas('uniqueVisitors', 2);
        $this->assertNotEquals(1256, $response->viewData('uniqueVisitors'));

        // Verify 30-day chart series
        $chartLabels = $response->viewData('chartLabels');
        $chartPageViews = $response->viewData('chartPageViews');
        $chartUniqueVisitors = $response->viewData('chartUniqueVisitors');

        $this->assertCount(30, $chartLabels);
        $this->assertCount(30, $chartPageViews);
        $this->assertCount(30, $chartUniqueVisitors);
        $this->assertEquals(3, array_sum($chartPageViews));
    }

    /**
     * Test that navigating public pages records PageView and updates unique visitors.
     */
    public function test_public_browsing_tracks_real_page_views_and_unique_visitors(): void
    {
        $this->assertEquals(0, PageView::count());

        // Visit homepage
        $this->get('/');
        $this->assertEquals(1, PageView::count());

        // Visit About Us in same session
        $this->get('/about');
        $this->assertEquals(2, PageView::count());

        // Total unique visitors in this session should be 1
        $uniqueVisitors = PageView::where('created_at', '>=', now()->subDays(30))
            ->distinct('session_id')
            ->count('session_id');
        $this->assertEquals(1, $uniqueVisitors);
    }

    /**
     * Test that viewing property detail increments views_count and appears in Top Properties.
     */
    public function test_property_views_increment_and_update_top_properties(): void
    {
        $initialViews = $this->property->views_count;

        $this->get(route('properties.show', $this->property->slug));

        $this->property->refresh();
        $this->assertEquals($initialViews + 1, $this->property->views_count);

        // Check Dashboard reflects updated view count
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $topProperties = $response->viewData('topProperties');
        $this->assertEquals($initialViews + 1, $topProperties->first()->views_count);
    }

    /**
     * Test that inquiry submission updates dashboard inquiries in real time.
     */
    public function test_new_inquiry_updates_dashboard_counts(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $this->assertEquals(1, $response->viewData('totalInquiries'));

        // Post new public inquiry
        $this->post(route('inquiry.store'), [
            'customer_name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '+62898765432',
            'subject' => 'General Inquiry',
            'message' => 'Looking for a villa in Lovina',
            'source' => 'contact_page',
        ]);

        // Refresh dashboard
        $response2 = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $this->assertEquals(2, $response2->viewData('totalInquiries'));
        $this->assertEquals(2, $response2->viewData('newInquiries'));
    }
}
