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

    /** @test */
    public function it_shows_analytics_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('analytics.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_returns_analytics_data(): void
    {
        $response = $this->actingAs($this->user)->get(route('analytics.index'));
        $response->assertViewHas('stats');
    }

    /** @test */
    public function it_filters_by_date_range(): void
    {
        $response = $this->actingAs($this->user)->get(route('analytics.index', [
            'date_from' => now()->subDays(30)->toDateString(),
            'date_to' => now()->toDateString(),
        ]));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_prevents_unauthorized_access(): void
    {
        $response = $this->get(route('analytics.index'));
        $response->assertRedirect(route('login'));
    }
}
