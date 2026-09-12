<?php

namespace Tests\Feature\Security;

use App\Models\Agency;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_xss_in_forms(): void
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->create(['agency_id' => $agency->id]);
        $response = $this->actingAs($user)->post(route('clients.store'), [
            'name' => '<script>alert("xss")</script>',
            'email' => 'test@example.com',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseMissing('clients', ['name' => '<script>alert("xss")</script>']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_cross_tenant_access(): void
    {
        $agency1 = Agency::factory()->create();
        $agency2 = Agency::factory()->create();
        $user1 = User::factory()->create(['agency_id' => $agency1->id]);
        $client = Client::factory()->create(['agency_id' => $agency2->id]);
        $response = $this->actingAs($user1)->get(route('clients.show', $client));
        $response->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_csrf_for_forms(): void
    {
        $route = app('router')->getRoutes()->getByName('clients.store');

        $this->assertContains(PreventRequestForgery::class, $route->gatherMiddleware());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_hashes_passwords(): void
    {
        $user = User::factory()->create(['password' => 'secret123']);
        $this->assertNotEquals('secret123', $user->password);
        $this->assertTrue(\Hash::check('secret123', $user->password));
    }
}
