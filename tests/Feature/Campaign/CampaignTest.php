<?php

namespace Tests\Feature\Campaign;

use App\Models\Agency;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $agency = Agency::factory()->create(['subscription_plan' => 'starter']);
        $this->user = User::factory()->create(['agency_id' => $agency->id, 'role' => 'owner']);
    }

    public function test_campaigns_index_requires_authentication(): void
    {
        $response = $this->get('/campaigns');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_campaigns(): void
    {
        $response = $this->actingAs($this->user)->get('/campaigns');
        $response->assertStatus(200);
    }

    public function test_user_can_create_campaign(): void
    {
        $response = $this->actingAs($this->user)->post('/campaigns', [
            'name' => 'Test Campaign',
            'type' => 'general',
            'description' => 'Test description',
        ]);

        $response->assertRedirect('/campaigns/1');
        $this->assertDatabaseHas('campaigns', ['name' => 'Test Campaign']);
    }

    public function test_user_can_view_campaign(): void
    {
        $campaign = Campaign::factory()->create(['agency_id' => $this->user->agency_id]);
        $response = $this->actingAs($this->user)->get("/campaigns/{$campaign->id}");
        $response->assertStatus(200);
    }

    public function test_user_cannot_view_other_agency_campaign(): void
    {
        $campaign = Campaign::factory()->create();
        $response = $this->actingAs($this->user)->get("/campaigns/{$campaign->id}");
        $response->assertStatus(403);
    }

    public function test_user_can_update_campaign(): void
    {
        $campaign = Campaign::factory()->create(['agency_id' => $this->user->agency_id]);
        $response = $this->actingAs($this->user)->put("/campaigns/{$campaign->id}", [
            'name' => 'Updated Campaign',
            'type' => 'general',
        ]);

        $response->assertRedirect("/campaigns/{$campaign->id}");
        $this->assertDatabaseHas('campaigns', ['name' => 'Updated Campaign']);
    }

    public function test_user_can_delete_campaign(): void
    {
        $campaign = Campaign::factory()->create(['agency_id' => $this->user->agency_id]);
        $response = $this->actingAs($this->user)->delete("/campaigns/{$campaign->id}");
        $response->assertRedirect('/campaigns');
        $this->assertSoftDeleted('campaigns', ['id' => $campaign->id]);
    }
}
