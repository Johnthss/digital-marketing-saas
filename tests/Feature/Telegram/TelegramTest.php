<?php

namespace Tests\Feature\Telegram;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelegramTest extends TestCase
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
    public function it_shows_telegram_link_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('telegram.link'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('telegram.link'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_requires_agency(): void
    {
        $user = User::factory()->create(['agency_id' => null]);
        $response = $this->actingAs($user)->get(route('telegram.link'));
        $response->assertForbidden();
    }
}
