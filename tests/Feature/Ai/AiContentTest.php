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
    public function it_shows_ai_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('ai.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_generates_ai_content(): void
    {
        $response = $this->actingAs($this->user)->post(route('ai.generate'), [
            'prompt' => 'Write a social post about AI',
            'tone' => 'professional',
            'length' => 'medium',
        ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function it_validates_ai_request(): void
    {
        $response = $this->actingAs($this->user)->post(route('ai.generate'), []);
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function it_shows_ai_ideas(): void
    {
        $response = $this->actingAs($this->user)->post(route('ai.ideas'), [
            'topic' => 'marketing',
        ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function it_prevents_unauthorized_access(): void
    {
        $response = $this->get(route('ai.index'));
        $response->assertRedirect(route('login'));
    }
}
