<?php

namespace Tests\Feature\Auth;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_login_page(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    #[Test]
    public function it_authenticates_user(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->create(['agency_id' => $agency->id, 'password' => bcrypt('password123')]);
        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function it_rejects_invalid_credentials(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'wrong@email.com',
            'password' => 'wrongpassword',
        ]);
        $response->assertSessionHasErrors();
    }

    #[Test]
    public function it_logs_out_user(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->create(['agency_id' => $agency->id]);
        $response = $this->actingAs($user)->post(route('logout'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_shows_registration_page(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
    }

    #[Test]
    public function it_registers_new_user(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agency_name' => 'Test Agency',
        ]);
        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertDatabaseHas('agencies', ['name' => 'Test Agency']);
    }

    #[Test]
    public function it_validates_registration(): void
    {
        $response = $this->post(route('register'), []);
        $response->assertSessionHasErrors(['name', 'email', 'password', 'agency_name']);
    }
}
