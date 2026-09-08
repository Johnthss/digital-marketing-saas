<?php

namespace Tests\Feature\Api;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAiTest extends TestCase
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
    public function test_it_validates_ai_generation_request(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/ai/generate', []);
        $response->assertUnprocessable();
    }

    /** @test */
    public function test_it_requires_auth_for_ai_generation(): void
    {
        $response = $this->postJson('/api/v1/ai/generate', [
            'action' => 'generate',
            'prompt' => 'Test prompt',
        ]);
        $response->assertUnauthorized();
    }
}
