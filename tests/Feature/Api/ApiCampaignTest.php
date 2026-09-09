<?php

namespace Tests\Feature\Api;

use App\Models\Agency;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCampaignTest extends TestCase
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

    public function test_it_lists_campaigns(): void
    {
        Campaign::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        
        $response = $this->actingAs($this->user)->getJson('/api/v1/campaigns');
        
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    public function test_it_filters_by_status(): void
    {
        Campaign::factory()->create(['agency_id' => $this->agency->id, 'status' => 'active']);
        Campaign::factory()->create(['agency_id' => $this->agency->id, 'status' => 'completed']);
        
        $response = $this->actingAs($this->user)->getJson('/api/v1/campaigns?status=active');
        
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_it_creates_a_campaign(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/campaigns', [
            'name' => 'Test Campaign',
            'type' => 'general',
            'description' => 'Test description',
        ]);
        
        $response->assertCreated();
        $this->assertDatabaseHas('campaigns', ['name' => 'Test Campaign']);
    }

    public function test_it_validates_campaign_creation(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/campaigns', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_it_shows_a_campaign(): void
    {
        $campaign = Campaign::factory()->create(['agency_id' => $this->agency->id]);
        
        $response = $this->actingAs($this->user)->getJson("/api/v1/campaigns/{$campaign->id}");
        
        $response->assertOk();
        $response->assertJsonPath('data.id', $campaign->id);
    }

    public function test_it_prevents_showing_other_agency_campaigns(): void
    {
        $otherAgency = Agency::factory()->create();
        $campaign = Campaign::factory()->create(['agency_id' => $otherAgency->id]);
        
        $response = $this->actingAs($this->user)->getJson("/api/v1/campaigns/{$campaign->id}");
        
        $response->assertNotFound();
    }

    public function test_it_updates_a_campaign(): void
    {
        $campaign = Campaign::factory()->create(['agency_id' => $this->agency->id]);
        
        $response = $this->actingAs($this->user)->putJson("/api/v1/campaigns/{$campaign->id}", [
            'name' => 'Updated Campaign',
        ]);
        
        $response->assertOk();
        $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'name' => 'Updated Campaign']);
    }

    public function test_it_deletes_a_campaign(): void
    {
        $campaign = Campaign::factory()->create(['agency_id' => $this->agency->id]);
        
        $response = $this->actingAs($this->user)->deleteJson("/api/v1/campaigns/{$campaign->id}");
        
        $response->assertNoContent();
        $this->assertSoftDeleted('campaigns', ['id' => $campaign->id]);
    }

    public function test_it_requires_auth(): void
    {
        $response = $this->getJson('/api/v1/campaigns');
        
        $response->assertUnauthorized();
    }
}