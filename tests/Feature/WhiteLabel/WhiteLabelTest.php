<?php

namespace Tests\Feature\WhiteLabel;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhiteLabelTest extends TestCase
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
    public function it_shows_white_label_settings(): void
    {
        $response = $this->actingAs($this->user)->get(route('white-label.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_updates_white_label_settings(): void
    {
        $response = $this->actingAs($this->user)->post(route('white-label.update'), [
            'brand_name' => 'My Brand',
            'brand_color' => '#ff0000',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('white_label_settings', [
            'agency_id' => $this->agency->id,
            'brand_name' => 'My Brand',
        ]);
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('white-label.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_requires_agency(): void
    {
        $user = User::factory()->create(['agency_id' => null]);
        $response = $this->actingAs($user)->get(route('white-label.index'));
        $response->assertForbidden();
    }
}
