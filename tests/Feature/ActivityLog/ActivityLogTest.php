<?php

namespace Tests\Feature\ActivityLog;

use App\Models\Agency;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
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
    public function it_lists_activity_logs(): void
    {
        ActivityLog::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('activity.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_shows_an_activity_log(): void
    {
        $log = ActivityLog::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('activity.show', $log));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_prevents_access_to_other_agency_logs(): void
    {
        $otherAgency = Agency::factory()->create();
        $log = ActivityLog::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('activity.show', $log));
        $response->assertForbidden();
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('activity.index'));
        $response->assertRedirect(route('login'));
    }
}
