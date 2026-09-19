<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Location;
use App\Models\CompanySetting;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthTest extends TestCase
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
        ]);

        $this->article = Article::create([
            'title' => 'Living in Lovina',
            'slug' => 'living-in-lovina',
            'category' => 'Guides',
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
    }

    /**
     * TEST A: Normal Login (Remember Me unchecked).
     */
    public function test_normal_login_without_remember_me(): void
    {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
        $response->assertSee('Admin Login');

        $loginResponse = $this->post(route('admin.login.submit'), [
            'email' => 'admin@lovinanorthbali.com',
            'password' => 'password',
            // remember not present
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);

        $recallerCookie = Auth::guard('web')->getRecallerName();
        $loginResponse->assertCookieMissing($recallerCookie);
    }

    /**
     * TEST B: Remember Me Enabled Login.
     */
    public function test_login_with_remember_me(): void
    {
        $loginResponse = $this->post(route('admin.login.submit'), [
            'email' => 'admin@lovinanorthbali.com',
            'password' => 'password',
            'remember' => '1',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);

        $recallerCookie = Auth::guard('web')->getRecallerName();
        $loginResponse->assertCookie($recallerCookie);
    }

    /**
     * TEST C: Explicit Logout clears authentication and persistent remember state, redirecting to Public Homepage.
     */
    public function test_explicit_logout_clears_remember_and_redirects_to_homepage(): void
    {
        // 1. Log in with remember me
        $loginResponse = $this->post(route('admin.login.submit'), [
            'email' => 'admin@lovinanorthbali.com',
            'password' => 'password',
            'remember' => '1',
        ]);

        $this->assertAuthenticatedAs($this->admin);

        // Capture user recaller token in DB
        $this->admin->refresh();
        $tokenBeforeLogout = $this->admin->remember_token;
        $this->assertNotEmpty($tokenBeforeLogout);

        // 2. Perform Logout
        $logoutResponse = $this->actingAs($this->admin)->post(route('admin.logout'));

        // 3. Confirm redirect to Public Homepage with success message
        $logoutResponse->assertRedirect(route('home'));
        $logoutResponse->assertSessionHas('success', 'Logged out successfully.');

        // 4. Confirm unauthenticated
        $this->assertGuest();

        // 5. Confirm remember token was cycled/changed in DB
        $this->admin->refresh();
        $this->assertNotEquals($tokenBeforeLogout, $this->admin->remember_token);

        // 6. Confirm recaller cookie is expired
        $recallerCookie = Auth::guard('web')->getRecallerName();
        $logoutResponse->assertCookieExpired($recallerCookie);

        // 7. Accessing Admin Portal now presents login page, NOT dashboard
        $portalResponse = $this->get(route('admin.login'));
        $portalResponse->assertStatus(200);
        $portalResponse->assertSee('Admin Login');
    }

    /**
     * TEST D: Admin can enable Remember Me again on subsequent login.
     */
    public function test_admin_can_enable_remember_me_again_after_logout(): void
    {
        // Initial login + logout
        $this->actingAs($this->admin)->post(route('admin.logout'));
        $this->assertGuest();

        // Second login with Remember Me
        $secondLogin = $this->post(route('admin.login.submit'), [
            'email' => 'admin@lovinanorthbali.com',
            'password' => 'password',
            'remember' => '1',
        ]);

        $secondLogin->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);

        $recallerCookie = Auth::guard('web')->getRecallerName();
        $secondLogin->assertCookie($recallerCookie);
    }

    /**
     * TEST E & F: Direct Admin URL access while logged out is protected.
     */
    public function test_unauthenticated_access_to_admin_urls_is_redirected_to_login(): void
    {
        // Direct /admin/dashboard
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect('/admin/login');

        // Direct /admin prefix
        $adminRoot = $this->get('/admin');
        $adminRoot->assertRedirect(route('admin.dashboard'));
        // Following redirect leads to /admin/login
        $followed = $this->followingRedirects()->get('/admin');
        $followed->assertSee('Admin Login');

        // Direct protected admin properties
        $propsResponse = $this->get(route('admin.properties.index'));
        $propsResponse->assertRedirect('/admin/login');
    }

    /**
     * TEST G: Direct /admin access while logged in redirects to dashboard.
     */
    public function test_authenticated_access_to_admin_root_redirects_to_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * TEST H: Accessing /admin/login while logged in redirects to dashboard.
     */
    public function test_authenticated_user_visiting_login_redirects_to_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.login'));
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * TEST I: Failed login with invalid credentials.
     */
    public function test_login_with_invalid_credentials_fails(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin@lovinanorthbali.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    /**
     * TEST J: Data Integrity — Logout NEVER deletes or modifies application data.
     */
    public function test_data_integrity_preserved_across_login_and_logout(): void
    {
        $propCountBefore = Property::count();
        $locCountBefore = Location::count();
        $catCountBefore = PropertyCategory::count();
        $artCountBefore = Article::count();
        $settCountBefore = CompanySetting::count();
        $userCountBefore = User::count();

        // Perform login & logout cycle
        $this->post(route('admin.login.submit'), [
            'email' => 'admin@lovinanorthbali.com',
            'password' => 'password',
            'remember' => '1',
        ]);
        $this->assertAuthenticatedAs($this->admin);

        $this->post(route('admin.logout'));
        $this->assertGuest();

        // Verify all application counts and records are identical
        $this->assertEquals($propCountBefore, Property::count());
        $this->assertEquals($locCountBefore, Location::count());
        $this->assertEquals($catCountBefore, PropertyCategory::count());
        $this->assertEquals($artCountBefore, Article::count());
        $this->assertEquals($settCountBefore, CompanySetting::count());
        $this->assertEquals($userCountBefore, User::count());

        $this->assertDatabaseHas('properties', [
            'id' => $this->property->id,
            'name' => 'Azure Vista Residence',
            'is_featured' => 1,
            'status' => 'published',
        ]);

        $this->assertDatabaseHas('locations', [
            'id' => $this->location->id,
            'name' => 'Lovina',
            'is_popular' => 1,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
            'email' => 'admin@lovinanorthbali.com',
        ]);
    }
}
