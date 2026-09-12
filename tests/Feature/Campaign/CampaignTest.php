<?php

namespace Tests\Feature\Campaign;

use App\Models\Agency;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CampaignTest extends TestCase
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

    #[Test]
    public function it_lists_campaigns(): void
    {
        Campaign::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('campaigns.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function it_creates_a_campaign(): void
    {
        $response = $this->actingAs($this->user)->post(route('campaigns.store'), [
            'name' => 'Test Campaign',
            'type' => 'general',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('campaigns', ['name' => 'Test Campaign']);
    }

    #[Test]
    public function it_validates_campaign_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('campaigns.store'), []);
        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function it_shows_a_campaign(): void
    {
        $campaign = Campaign::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('campaigns.show', $campaign));
        $response->assertStatus(200);
    }

    #[Test]
    public function it_updates_a_campaign(): void
    {
        $campaign = Campaign::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->put(route('campaigns.update', $campaign), [
            'name' => 'Updated Campaign',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'name' => 'Updated Campaign']);
    }

    #[Test]
    public function it_deletes_a_campaign(): void
    {
        $campaign = Campaign::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('campaigns.destroy', $campaign));
        $response->assertRedirect();
        $this->assertSoftDeleted('campaigns', ['id' => $campaign->id]);
    }

    #[Test]
    public function it_prevents_access_to_other_agency_campaigns(): void
    {
        $otherAgency = Agency::factory()->create();
        $campaign = Campaign::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('campaigns.show', $campaign));
        $response->assertForbidden();
    }

    #[Test]
    public function it_requires_auth(): void
    {
        $response = $this->get(route('campaigns.index'));
        $response->assertRedirect(route('login'));
    }
}
