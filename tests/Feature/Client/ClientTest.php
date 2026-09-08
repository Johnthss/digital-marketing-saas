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

    /** @test */
    public function it_lists_clients(): void
    {
        Client::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('clients.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_creates_a_client(): void
    {
        $response = $this->actingAs($this->user)->post(route('clients.store'), [
            'name' => 'Test Client',
            'email' => 'test@example.com',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('clients', ['name' => 'Test Client']);
    }

    /** @test */
    public function it_validates_client_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('clients.store'), []);
        $response->assertSessionHasErrors(['name', 'email']);
    }

    /** @test */
    public function it_shows_a_client(): void
    {
        $client = Client::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('clients.show', $client));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_updates_a_client(): void
    {
        $client = Client::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->put(route('clients.update', $client), [
            'name' => 'Updated Client',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'Updated Client']);
    }

    /** @test */
    public function it_deletes_a_client(): void
    {
        $client = Client::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('clients.destroy', $client));
        $response->assertRedirect();
        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    /** @test */
    public function it_prevents_access_to_other_agency_clients(): void
    {
        $otherAgency = Agency::factory()->create();
        $client = Client::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('clients.show', $client));
        $response->assertForbidden();
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('clients.index'));
        $response->assertRedirect(route('login'));
    }
}
