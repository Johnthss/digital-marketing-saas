<?php

namespace Tests\Feature\Dashboard;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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
    public function it_shows_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_shows_quick_stats(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertViewHas('quickStats');
    }

    /** @test */
    public function it_shows_recent_activity(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertViewHas('recentActivity');
    }

    /** @test */
    public function it_shows_performance_data(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertViewHas('performanceData');
    }

    /** @test */
    public function it_shows_upcoming_posts(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertViewHas('upcomingPosts');
    }

    /** @test */
    public function it_requires_authentication(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_requires_agency(): void
    {
        $userWithoutAgency = User::factory()->create(['agency_id' => null]);
        $response = $this->actingAs($userWithoutAgency)->get(route('dashboard'));
        $response->assertForbidden();
    }
}
