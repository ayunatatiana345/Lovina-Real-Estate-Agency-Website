<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\CompanySetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class AdminPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Lovina Agency Admin',
            'email' => 'admin@lovinanorthbali.com',
            'password' => Hash::make('oldpassword123'),
        ]);

        CompanySetting::create([
            'company_name' => 'PT Lovina North Bali Real Estate Agency',
            'company_email' => 'info@lovinanorthbali.com',
            'company_phone' => '+62 812 3456 7890',
            'company_address' => 'Jl. Raya Lovina, Kalibukbuk, Buleleng, Bali',
        ]);
    }

    /**
     * Test A: Existing Login page displays the "Forgot Password?" link.
     */
    public function test_login_page_displays_forgot_password_link(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        $response->assertSee('Forgot Password?');
        $response->assertSee(route('admin.password.request'));
    }

    /**
     * Test B: Forgot Password page renders correctly.
     */
    public function test_forgot_password_page_renders_correctly(): void
    {
        $response = $this->get(route('admin.password.request'));

        $response->assertStatus(200);
        $response->assertSee('Forgot Password');
        $response->assertSee('Send Password Reset Link');
        $response->assertSee('Email Address');
        $response->assertSee(route('admin.login'));
    }

    /**
     * Test C: Validation on Forgot Password submission.
     */
    public function test_send_reset_link_validation(): void
    {
        // Missing email
        $response = $this->post(route('admin.password.email'), [
            'email' => '',
        ]);
        $response->assertSessionHasErrors('email');

        // Invalid email format
        $response = $this->post(route('admin.password.email'), [
            'email' => 'not-an-email',
        ]);
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test D: Sending reset link for non-existent admin email fails gracefully.
     */
    public function test_send_reset_link_to_non_existent_email(): void
    {
        $response = $this->post(route('admin.password.email'), [
            'email' => 'unknown@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test E: Sending reset link sends notification to registered admin.
     */
    public function test_send_reset_link_sends_notification_to_admin(): void
    {
        Notification::fake();

        $response = $this->post(route('admin.password.email'), [
            'email' => 'admin@lovinanorthbali.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo(
            $this->admin,
            ResetPasswordNotification::class,
            function ($notification) {
                return !empty($notification->token);
            }
        );
    }

    /**
     * Test F: Reset Password page renders correctly with token and prefilled email.
     */
    public function test_reset_password_page_renders_with_token(): void
    {
        $token = 'sample-reset-token-123';
        $response = $this->get(route('password.reset', [
            'token' => $token,
            'email' => 'admin@lovinanorthbali.com',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Set New Password');
        $response->assertSee('admin@lovinanorthbali.com');
        $response->assertSee('New Password');
        $response->assertSee('Confirm New Password');
        $response->assertSee('Reset Password');
        $response->assertSee($token);
    }

    /**
     * Test G: Reset Password validation rules.
     */
    public function test_reset_password_validation_rules(): void
    {
        $token = Password::broker()->createToken($this->admin);

        // Password confirmation mismatch
        $response = $this->post(route('admin.password.update'), [
            'token' => $token,
            'email' => 'admin@lovinanorthbali.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);
        $response->assertSessionHasErrors('password');

        // Password too short (< 8 chars)
        $response = $this->post(route('admin.password.update'), [
            'token' => $token,
            'email' => 'admin@lovinanorthbali.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);
        $response->assertSessionHasErrors('password');

        // Invalid token
        $response = $this->post(route('admin.password.update'), [
            'token' => 'invalid-token-xyz',
            'email' => 'admin@lovinanorthbali.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test H: Complete password reset flow, token invalidation, and login with new password.
     */
    public function test_successful_password_reset_and_login(): void
    {
        $broker = Password::broker();
        $token = $broker->createToken($this->admin);

        $this->assertTrue($broker->tokenExists($this->admin, $token));

        $response = $this->post(route('admin.password.update'), [
            'token' => $token,
            'email' => 'admin@lovinanorthbali.com',
            'password' => 'newSecurePass2026!',
            'password_confirmation' => 'newSecurePass2026!',
        ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('status');

        // Follow redirect to login page and ensure status message is visible
        $loginPageResponse = $this->get(route('admin.login'));
        $loginPageResponse->assertStatus(200);

        // Verify token has been consumed and invalidated
        $this->assertFalse($broker->tokenExists($this->admin, $token));

        // Verify old password does not work
        $this->assertFalse(Auth::attempt([
            'email' => 'admin@lovinanorthbali.com',
            'password' => 'oldpassword123',
        ]));

        // Verify new password works
        $this->assertTrue(Auth::attempt([
            'email' => 'admin@lovinanorthbali.com',
            'password' => 'newSecurePass2026!',
        ]));

        // Test logging in via the controller with new password
        $loginResponse = $this->post(route('admin.login.submit'), [
            'email' => 'admin@lovinanorthbali.com',
            'password' => 'newSecurePass2026!',
        ]);
        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin->fresh());
    }

    /**
     * Test I: Authenticated admin is redirected to dashboard if accessing forgot/reset password pages.
     */
    public function test_authenticated_admin_redirected_to_dashboard(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.password.request'));
        $response->assertRedirect(route('admin.dashboard'));

        $response = $this->get(route('password.reset', ['token' => 'dummy']));
        $response->assertRedirect(route('admin.dashboard'));
    }
}
