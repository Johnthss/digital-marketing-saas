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
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agency = Agency::factory()->create();
        $this->user = User::factory()->create(['agency_id' => $this->agency->id]);
    }

    /** @test */
    public function it_shows_agency_settings(): void
    {
        $response = $this->actingAs($this->user)->get(route('agency.settings'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_updates_agency_settings(): void
    {
        $response = $this->actingAs($this->user)->put(route('agency.settings.update'), [
            'name' => 'Updated Agency',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('agencies', ['id' => $this->agency->id, 'name' => 'Updated Agency']);
    }

    /** @test */
    public function it_shows_team_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('agency.team'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_invites_team_member(): void
    {
        $response = $this->actingAs($this->user)->post(route('agency.team.invite'), [
            'email' => 'newmember@example.com',
            'role' => 'member',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'newmember@example.com']);
    }

    /** @test */
    public function it_prevents_access_to_other_agency(): void
    {
        $otherAgency = Agency::factory()->create();
        $response = $this->actingAs($this->user)->put(route('agency.settings.update'), [
            'name' => 'Hacked Agency',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('agencies', ['id' => $otherAgency->id, 'name' => 'Hacked Agency']);
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('agency.settings'));
        $response->assertRedirect(route('login'));
    }
}
