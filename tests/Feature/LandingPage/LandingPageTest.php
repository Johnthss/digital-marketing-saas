<?php

namespace Tests\Feature\LandingPage;

use App\Models\Agency;
use App\Models\LandingPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
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
    public function it_lists_landing_pages(): void
    {
        LandingPage::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('landing-pages.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_creates_a_landing_page(): void
    {
        $response = $this->actingAs($this->user)->post(route('landing-pages.store'), [
            'name' => 'Test Page',
            'slug' => 'test-page',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('landing_pages', ['name' => 'Test Page']);
    }

    /** @test */
    public function it_validates_landing_page_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('landing-pages.store'), []);
        $response->assertSessionHasErrors(['name', 'slug']);
    }

    /** @test */
    public function it_shows_a_landing_page(): void
    {
        $page = LandingPage::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('landing-pages.show', $page));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_deletes_a_landing_page(): void
    {
        $page = LandingPage::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('landing-pages.destroy', $page));
        $response->assertRedirect();
        $this->assertSoftDeleted('landing_pages', ['id' => $page->id]);
    }

    /** @test */
    public function it_prevents_access_to_other_agency_pages(): void
    {
        $otherAgency = Agency::factory()->create();
        $page = LandingPage::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('landing-pages.show', $page));
        $response->assertForbidden();
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('landing-pages.index'));
        $response->assertRedirect(route('login'));
    }
}
