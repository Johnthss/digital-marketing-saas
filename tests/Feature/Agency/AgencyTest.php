<?php

namespace Tests\Feature\Agency;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgencyTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agency = Agency::factory()->create();
        $this->owner = User::factory()->create(['agency_id' => $this->agency->id, 'role' => 'owner']);
    }

    /** @test */
    public function it_shows_agency_settings(): void
    {
        $response = $this->actingAs($this->owner)->get(route('agency.settings'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_updates_agency_settings(): void
    {
        $response = $this->actingAs($this->owner)->put(route('agency.settings'), [
            'name' => 'Updated Agency',
            'timezone' => 'UTC',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('agencies', ['id' => $this->agency->id, 'name' => 'Updated Agency']);
    }

    /** @test */
    public function it_shows_team_page(): void
    {
        $response = $this->actingAs($this->owner)->get(route('agency.team'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_invites_team_member(): void
    {
        $response = $this->actingAs($this->owner)->post(route('agency.team.invite'), [
            'email' => 'newmember@example.com',
            'role' => 'member',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'newmember@example.com']);
    }

    /** @test */
    public function it_removes_team_member(): void
    {
        $member = User::factory()->create(['agency_id' => $this->agency->id, 'role' => 'member']);
        $response = $this->actingAs($this->owner)->delete(route('agency.team.remove', $member));
        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $member->id]);
    }

    /** @test */
    public function it_updates_member_role(): void
    {
        $member = User::factory()->create(['agency_id' => $this->agency->id, 'role' => 'member']);
        $response = $this->actingAs($this->owner)->put(route('agency.team.role', $member), [
            'role' => 'admin',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $member->id, 'role' => 'admin']);
    }

    /** @test */
    public function it_prevents_non_owner_team_management(): void
    {
        $member = User::factory()->create(['agency_id' => $this->agency->id, 'role' => 'member']);
        $response = $this->actingAs($member)->get(route('agency.team'));
        $response->assertForbidden();
    }
}
