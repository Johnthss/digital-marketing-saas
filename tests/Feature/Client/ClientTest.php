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

    private Agency $agency;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agency = Agency::factory()->create();
        $this->user = User::factory()->create(['agency_id' => $this->agency->id]);
    }

    public function test_it_lists_clients(): void
    {
        Client::factory()->count(3)->create(['agency_id' => $this->agency->id]);

        $response = $this->actingAs($this->user)->get(route('clients.index'));

        $response->assertOk();
        $response->assertViewIs('clients.index');
        $response->assertViewHas('clients');
    }

    public function test_it_creates_a_client(): void
    {
        $response = $this->actingAs($this->user)->post(route('clients.store'), [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'phone' => '+1234567890',
            'company' => 'Test Company',
            'industry' => 'Technology',
            'notes' => 'Some notes',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'agency_id' => $this->agency->id,
        ]);
    }

    public function test_it_validates_client_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('clients.store'), []);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_it_shows_a_client(): void
    {
        $client = Client::factory()->create(['agency_id' => $this->agency->id]);

        $response = $this->actingAs($this->user)->get(route('clients.show', $client));

        $response->assertOk();
        $response->assertViewIs('clients.show');
        $response->assertViewHas('client');
    }

    public function test_it_prevents_showing_other_agency_clients(): void
    {
        $otherAgency = Agency::factory()->create();
        $client = Client::factory()->create(['agency_id' => $otherAgency->id]);

        $response = $this->actingAs($this->user)->get(route('clients.show', $client));

        $response->assertForbidden();
    }

    public function test_it_edits_a_client(): void
    {
        $client = Client::factory()->create(['agency_id' => $this->agency->id]);

        $response = $this->actingAs($this->user)->get(route('clients.edit', $client));

        $response->assertOk();
        $response->assertViewIs('clients.edit');
    }

    public function test_it_updates_a_client(): void
    {
        $client = Client::factory()->create(['agency_id' => $this->agency->id]);

        $response = $this->actingAs($this->user)->put(route('clients.update', $client), [
            'name' => 'Updated Client',
            'email' => 'updated@example.com',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated Client',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_it_deletes_a_client(): void
    {
        $client = Client::factory()->create(['agency_id' => $this->agency->id]);

        $response = $this->actingAs($this->user)->delete(route('clients.destroy', $client));

        $response->assertRedirect(route('clients.index'));
        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_it_requires_auth(): void
    {
        $response = $this->get(route('clients.index'));

        $response->assertRedirect(route('login'));
    }
}
