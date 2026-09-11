<?php

namespace Tests\Feature\Api;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agency = Agency::factory()->create();
        $this->user = User::factory()->create(['agency_id' => $this->agency->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_health_status(): void
    {
        $response = $this->getJson('/api/health');
        $response->assertOk();
        $response->assertJson(['status' => 'ok']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_version_info(): void
    {
        $response = $this->getJson('/api/version');
        $response->assertOk();
        $response->assertJsonStructure(['version', 'name']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_auth_for_protected_endpoints(): void
    {
        $response = $this->getJson('/api/v1/social-posts');
        $response->assertUnauthorized();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_404_for_unknown_endpoints(): void
    {
        $response = $this->getJson('/api/unknown');
        $response->assertNotFound();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_openapi_docs(): void
    {
        $response = $this->get('/api/docs');
        $response->assertOk();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_openapi_yaml(): void
    {
        $response = $this->get('/api/docs/openapi.yaml');
        $response->assertOk();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_openapi_json(): void
    {
        $response = $this->get('/api/docs/openapi.json');
        $response->assertOk();
    }
}
