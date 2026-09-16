<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\Inquiry;
use App\Models\InquiryStatusLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InquiryFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $property;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@lovinanorthbali.com',
            'password' => bcrypt('password'),
        ]);

        $category = PropertyCategory::create([
            'name' => 'Land',
            'slug' => 'land',
            'status' => 'active',
        ]);

        $location = Location::create([
            'name' => 'Kaliasem',
            'slug' => 'kaliasem',
            'status' => 'active',
        ]);

        $this->property = Property::create([
            'category_id' => $category->id,
            'location_id' => $location->id,
            'name' => '6 Are Land in Kaliasem with Good Access',
            'slug' => '6-are-land-in-kaliasem-with-good-access',
            'price' => 500000000,
            'description' => 'Beautiful land in Kaliasem.',
            'status' => 'published',
            'is_featured' => true,
        ]);
    }

    public function test_property_detail_form_creates_inquiry_and_status_log(): void
    {
        $response = $this->post(route('inquiry.store'), [
            'customer_name' => 'Inquiry Test User',
            'email' => 'inquiry-test@example.com',
            'phone' => '+62 800 0000 0000',
            'property_id' => $this->property->id,
            'message' => 'CONTROLLED TEST — Submitted from the public website to verify the real Inquiry flow.',
            'source' => 'Property Detail Page',
        ]);

        $response->assertSessionHas('success_modal', true);
        $this->assertDatabaseHas('inquiries', [
            'customer_name' => 'Inquiry Test User',
            'email' => 'inquiry-test@example.com',
            'phone' => '+62 800 0000 0000',
            'property_id' => $this->property->id,
            'status' => 'new',
            'source' => 'Property Detail Page',
        ]);

        $inquiry = Inquiry::where('email', 'inquiry-test@example.com')->first();
        $this->assertNotNull($inquiry);
        $this->assertDatabaseHas('inquiry_status_logs', [
            'inquiry_id' => $inquiry->id,
            'status' => 'new',
        ]);
    }

    public function test_general_contact_inquiry_ajax_creates_inquiry_with_null_property(): void
    {
        $response = $this->postJson(route('inquiry.store'), [
            'customer_name' => 'General Inquiry User',
            'email' => 'general@example.com',
            'phone' => '+62 811 2222 3333',
            'property_id' => null,
            'message' => 'General inquiry message.',
            'source' => 'Contact Us Form',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('inquiries', [
            'customer_name' => 'General Inquiry User',
            'email' => 'general@example.com',
            'property_id' => null,
            'status' => 'new',
            'subject' => 'General Inquiry',
        ]);
    }

    public function test_defensive_aliasing_for_name_and_property(): void
    {
        $response = $this->postJson(route('inquiry.store'), [
            'name' => 'Aliased User',
            'email' => 'aliased@example.com',
            'phone' => '+62 812 3456 7890',
            'property' => $this->property->id,
            'message' => 'Testing field alias.',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('inquiries', [
            'customer_name' => 'Aliased User',
            'property_id' => $this->property->id,
        ]);
    }

    public function test_invalid_submission_fails_validation_and_creates_no_record(): void
    {
        $response = $this->postJson(route('inquiry.store'), [
            'customer_name' => '',
            'email' => 'not-an-email',
            'phone' => '',
            'message' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['customer_name', 'email', 'phone', 'message']);
        $this->assertEquals(0, Inquiry::count());
    }

    public function test_unauthenticated_user_redirected_from_admin_inquiries(): void
    {
        $response = $this->get(route('admin.inquiries.index'));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_inquiries_and_search_by_property_name(): void
    {
        $inquiry = Inquiry::create([
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+62 812 0000 1111',
            'property_id' => $this->property->id,
            'message' => 'Searching for land.',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.inquiries.index', ['search' => 'Kaliasem']));
        $response->assertStatus(200);
        $response->assertSee('Jane Doe');
        $response->assertSee('6 Are Land in Kaliasem with Good Access');
    }

    public function test_admin_can_update_inquiry_status(): void
    {
        $inquiry = Inquiry::create([
            'customer_name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'phone' => '+62 812 9999 8888',
            'property_id' => $this->property->id,
            'message' => 'Status update test.',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.inquiries.update', $inquiry->id), [
            'status' => 'in_progress',
            'admin_notes' => 'Customer contacted via WhatsApp.',
        ]);

        $response->assertSessionHas('success');
        $inquiry->refresh();
        $this->assertEquals('in_progress', $inquiry->status);
        $this->assertEquals('Customer contacted via WhatsApp.', $inquiry->admin_notes);
        $this->assertDatabaseHas('inquiry_status_logs', [
            'inquiry_id' => $inquiry->id,
            'status' => 'in_progress',
        ]);
    }
}
