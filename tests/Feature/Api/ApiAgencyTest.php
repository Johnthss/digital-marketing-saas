<?php

namespace Tests\Feature\Api;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAgencyTest extends TestCase
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
    public function test_it_returns_agency_settings(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/v1/agency/settings');
        $response->assertOk();
        $response->assertJsonPath('data.id', $this->agency->id);
    }

    /** @test */
    public function test_it_updates_agency_settings(): void
    {
        $response = $this->actingAs($this->user)->putJson('/api/v1/agency/settings', [
            'name' => 'Updated Agency',
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('agencies', ['id' => $this->agency->id, 'name' => 'Updated Agency']);
    }

    /** @test */
    public function test_it_returns_team_members(): void
    {
        User::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->getJson('/api/v1/agency/team');
        $response->assertOk();
        $response->assertJsonCount(4, 'data');
    }

    /** @test */
    public function test_it_returns_billing_info(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/v1/agency/billing');
        $response->assertOk();
    }

    /** @test */
    public function test_it_requires_auth(): void
    {
        $response = $this->getJson('/api/v1/agency/settings');
        $response->assertUnauthorized();
    }
}
