<?php

namespace Tests\Feature\Auth;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'agency_name' => 'Test Agency',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('agencies', ['name' => 'Test Agency']);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'owner']);
    }

    public function test_registration_creates_agency_and_user(): void
    {
        $this->post('/register', [
            'agency_name' => 'My Agency',
            'name' => 'Owner Name',
            'email' => 'owner@agency.com',
            'password' => 'secure123',
            'password_confirmation' => 'secure123',
        ]);

        $user = User::where('email', 'owner@agency.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->agency);
        $this->assertEquals('My Agency', $user->agency->name);
        $this->assertEquals('owner', $user->role);
    }

    public function test_registration_requires_valid_email(): void
    {
        $response = $this->post('/register', [
            'agency_name' => 'Test Agency',
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'agency_name' => 'Test Agency',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}
