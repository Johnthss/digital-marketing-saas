<?php

namespace Tests\Feature\Analytics;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
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

    public function test_it_shows_analytics_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('analytics.index'));

        $response->assertOk();
        $response->assertViewIs('analytics.index');
        $response->assertViewHas('postStats');
        $response->assertViewHas('engagement');
        $response->assertViewHas('platformStats');
    }

    public function test_it_requires_auth(): void
    {
        $response = $this->get(route('analytics.index'));

        $response->assertRedirect(route('login'));
    }
}
