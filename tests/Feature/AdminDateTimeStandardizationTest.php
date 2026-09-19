<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Inquiry;
use App\Models\InquiryStatusLog;
use App\Models\Article;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\CompanySetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AdminDateTimeStandardizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected PropertyCategory $category;
    protected Location $location;
    protected Property $property;
    protected Article $article;
    protected Inquiry $inquiry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@lovinarealestate.com',
            'password' => bcrypt('AdminPassword123!'),
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

        // Fix datetime to a known point: 2026-09-18 15:30:00 WITA
        Carbon::setTestNow(Carbon::create(2026, 9, 18, 15, 30, 0, 'Asia/Makassar'));

        $this->property = Property::create([
            'name' => 'Sunset Ocean Villa',
            'slug' => 'sunset-ocean-villa',
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'price' => 4500000000,
            'status' => 'published',
            'is_featured' => true,
        ]);

        $this->article = Article::create([
            'title' => 'Top Reasons to Invest in Lovina',
            'slug' => 'top-reasons-to-invest-in-lovina',
            'category' => 'Investment',
            'content' => 'Comprehensive investment guide content.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->inquiry = Inquiry::create([
            'customer_name' => 'Sarah Connor',
            'email' => 'sarah@example.com',
            'phone' => '+628123456789',
            'property_id' => $this->property->id,
            'subject' => 'Viewing Request for Sunset Ocean Villa',
            'message' => 'I would like to arrange a viewing tomorrow.',
            'source' => 'Property Detail Page',
            'status' => 'new',
        ]);

        InquiryStatusLog::create([
            'inquiry_id' => $this->inquiry->id,
            'status' => 'new',
            'changed_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset mocked time
        parent::tearDown();
    }

    /**
     * Verify that Laravel application timezone is set to Asia/Makassar (WITA / UTC+8).
     */
    public function test_application_timezone_is_asia_makassar(): void
    {
        $this->assertEquals('Asia/Makassar', config('app.timezone'));
    }

    /**
     * Verify that Carbon WITA macro produces the standardized format: '18 Sep 2026, 03:30 PM WITA'.
     */
    public function test_carbon_to_wita_format_macro(): void
    {
        $testDate = Carbon::create(2026, 9, 18, 15, 30, 0, 'Asia/Makassar');
        $this->assertEquals('18 Sep 2026, 03:30 PM WITA', $testDate->toWitaFormat());
    }

    /**
     * Verify Admin Dashboard displays inquiries with standardized WITA & AM/PM timestamps.
     */
    public function test_admin_dashboard_displays_wita_timestamps(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('18 Sep 2026, 03:30 PM WITA');
    }

    /**
     * Verify Admin Inquiries index displays standardized WITA & AM/PM timestamps.
     */
    public function test_admin_inquiries_index_displays_wita_timestamps(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.inquiries.index'));
        $response->assertStatus(200);
        $response->assertSee('18 Sep 2026, 03:30 PM WITA');
    }

    /**
     * Verify Admin Inquiry Detail displays standardized WITA & AM/PM timestamps.
     */
    public function test_admin_inquiry_show_displays_wita_timestamps(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.inquiries.show', $this->inquiry->id));
        $response->assertStatus(200);
        $response->assertSee('Received on 18 Sep 2026, 03:30 PM WITA');
        $response->assertSee('18 Sep 2026, 03:30 PM WITA');
    }

    /**
     * Verify Admin Articles index displays standardized WITA & AM/PM timestamps.
     */
    public function test_admin_articles_index_displays_wita_timestamps(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.articles.index'));
        $response->assertStatus(200);
        $response->assertSee('18 Sep 2026, 03:30 PM WITA');
    }

    /**
     * Verify Admin Properties index displays standardized WITA & AM/PM timestamps.
     */
    public function test_admin_properties_index_displays_wita_timestamps(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.properties.index'));
        $response->assertStatus(200);
        $response->assertSee('18 Sep 2026, 03:30 PM WITA');
    }

    /**
     * Verify Company Settings currency last refreshed display formats in WITA.
     */
    public function test_admin_settings_currency_last_refreshed_displays_wita(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertSee('WITA');
        $response->assertSee('Rate Publication Date');
        $response->assertSee('Last Refreshed');
    }

    /**
     * Verify live inquiry submission properly records database timestamp and renders WITA format.
     */
    public function test_public_inquiry_submission_renders_accurate_wita_in_admin(): void
    {
        // Public user submits inquiry at 04:45 PM WITA
        Carbon::setTestNow(Carbon::create(2026, 9, 18, 16, 45, 0, 'Asia/Makassar'));

        $postData = [
            'customer_name' => 'Michael Scott',
            'email' => 'michael@dundermifflin.com',
            'phone' => '+628987654321',
            'property_id' => $this->property->id,
            'subject' => 'Inquiry for Sunset Ocean Villa',
            'message' => 'Please provide full villa specifications.',
        ];

        $postResponse = $this->post(route('inquiry.store'), $postData);
        $postResponse->assertStatus(302);

        $newInquiry = Inquiry::where('email', 'michael@dundermifflin.com')->first();
        $this->assertNotNull($newInquiry);

        // Verify that Admin Inquiries view shows the new inquiry with 04:45 PM WITA
        $adminResponse = $this->actingAs($this->admin)->get(route('admin.inquiries.index'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Michael Scott');
        $adminResponse->assertSee('18 Sep 2026, 04:45 PM WITA');
    }
}
