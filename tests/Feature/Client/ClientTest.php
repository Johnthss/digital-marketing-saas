<?php

namespace Tests\Feature\Client;

use App\Models\Agency;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $agency = Agency::factory()->create(['subscription_plan' => 'starter']);
        $this->user = User::factory()->create(['agency_id' => $agency->id, 'role' => 'owner']);
    }

    public function test_clients_index_requires_authentication(): void
    {
        $response = $this->get('/clients');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_clients(): void
    {
        $response = $this->actingAs($this->user)->get('/clients');
        $response->assertStatus(200);
    }

    public function test_user_can_create_client(): void
    {
        $response = $this->actingAs($this->user)->post('/clients', [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'company' => 'Test Company',
        ]);

        $response->assertRedirect('/clients/1');
        $this->assertDatabaseHas('clients', ['name' => 'Test Client']);
    }

    public function test_user_can_view_client(): void
    {
        $client = Client::factory()->create(['agency_id' => $this->user->agency_id]);
        $response = $this->actingAs($this->user)->get("/clients/{$client->id}");
        $response->assertStatus(200);
    }

    public function test_user_cannot_view_other_agency_client(): void
    {
        $client = Client::factory()->create();
        $response = $this->actingAs($this->user)->get("/clients/{$client->id}");
        $response->assertStatus(403);
    }

    public function test_user_can_update_client(): void
    {
        $client = Client::factory()->create(['agency_id' => $this->user->agency_id]);
        $response = $this->actingAs($this->user)->put("/clients/{$client->id}", [
            'name' => 'Updated Client',
            'email' => $client->email,
            'status' => 'active',
        ]);

        $response->assertRedirect("/clients/{$client->id}");
        $this->assertDatabaseHas('clients', ['name' => 'Updated Client']);
    }

    public function test_user_can_delete_client(): void
    {
        $client = Client::factory()->create(['agency_id' => $this->user->agency_id]);
        $response = $this->actingAs($this->user)->delete("/clients/{$client->id}");
        $response->assertRedirect('/clients');
        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }
}
