<?php

namespace Tests\Feature\Search;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
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
    public function it_shows_search_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('search.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_searches_content(): void
    {
        $response = $this->actingAs($this->user)->get(route('search.index', ['q' => 'test']));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('search.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_requires_agency(): void
    {
        $user = User::factory()->create(['agency_id' => null]);
        $response = $this->actingAs($user)->get(route('search.index'));
        $response->assertForbidden();
    }
}
