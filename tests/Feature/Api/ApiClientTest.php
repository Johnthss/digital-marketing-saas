<?php

namespace Tests\Feature\Api;

use App\Models\Agency;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiClientTest extends TestCase
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

    /** @test */
    public function it_lists_clients(): void
    {
        Client::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->getJson('/api/v1/clients');
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_creates_a_client(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/clients', [
            'name' => 'Test Client',
            'email' => 'test@example.com',
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('clients', ['name' => 'Test Client']);
    }

    /** @test */
    public function it_validates_client_creation(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/clients', []);
        $response->assertUnprocessable();
    }

    /** @test */
    public function it_shows_a_client(): void
    {
        $client = Client::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->getJson("/api/v1/clients/{$client->id}");
        $response->assertOk();
        $response->assertJsonPath('data.id', $client->id);
    }

    /** @test */
    public function it_prevents_access_to_other_agency_clients(): void
    {
        $otherAgency = Agency::factory()->create();
        $client = Client::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->getJson("/api/v1/clients/{$client->id}");
        $response->assertNotFound();
    }
}
