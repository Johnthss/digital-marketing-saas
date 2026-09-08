<?php

namespace Tests\Feature\Api;

use App\Models\Agency;
use App\Models\AiContentLog;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiDashboardTest extends TestCase
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
    public function test_it_returns_dashboard_stats(): void
    {
        Client::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        SocialPost::factory()->count(5)->create(['agency_id' => $this->agency->id]);
        Campaign::factory()->count(2)->create(['agency_id' => $this->agency->id]);
        Invoice::factory()->count(4)->create(['agency_id' => $this->agency->id, 'status' => 'paid']);

        $response = $this->actingAs($this->user)->getJson('/api/v1/dashboard');
        $response->assertOk();
        $response->assertJsonPath('overview.total_clients', 3);
        $response->assertJsonPath('overview.total_posts', 5);
        $response->assertJsonPath('overview.total_campaigns', 2);
        $response->assertJsonPath('social.total_posts', 5);
    }

    /** @test */
    public function test_it_returns_ai_stats(): void
    {
        AiContentLog::factory()->count(3)->create([
            'agency_id' => $this->agency->id,
            'status' => 'success',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/v1/dashboard');
        $response->assertOk();
        $response->assertJsonPath('ai.total_generations', 3);
        $response->assertJsonPath('ai.successful', 3);
    }

    /** @test */
    public function test_it_requires_auth(): void
    {
        $response = $this->getJson('/api/v1/dashboard');
        $response->assertUnauthorized();
    }
}
