<?php

namespace Tests\Feature\Ai;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiContentTest extends TestCase
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
    public function it_shows_ai_content_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('ai.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_validates_ai_generation(): void
    {
        $response = $this->actingAs($this->user)->post(route('ai.generate'), []);
        $response->assertSessionHasErrors(['prompt']);
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('ai.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_requires_agency(): void
    {
        $user = User::factory()->create(['agency_id' => null]);
        $response = $this->actingAs($user)->get(route('ai.index'));
        $response->assertForbidden();
    }
}
