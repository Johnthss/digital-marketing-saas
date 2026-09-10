<?php

namespace Tests\Feature\ContentLibrary;

use App\Models\Agency;
use App\Models\ContentAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentLibraryTest extends TestCase
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
    public function it_lists_content_assets(): void
    {
        ContentAsset::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('content.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_creates_content(): void
    {
        $response = $this->actingAs($this->user)->post(route('content.store'), [
            'name' => 'Test Content',
            'type' => 'post',
            'content' => 'Test content body',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('content_assets', ['name' => 'Test Content']);
    }

    /** @test */
    public function it_validates_content_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('content.store'), []);
        $response->assertSessionHasErrors(['name', 'type', 'content']);
    }

    /** @test */
    public function it_shows_content(): void
    {
        $asset = ContentAsset::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('content.show', $asset));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_deletes_content(): void
    {
        $asset = ContentAsset::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('content.destroy', $asset));
        $response->assertRedirect();
        $this->assertSoftDeleted('content_assets', ['id' => $asset->id]);
    }

    /** @test */
    public function it_prevents_access_to_other_agency_content(): void
    {
        $otherAgency = Agency::factory()->create();
        $asset = ContentAsset::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('content.show', $asset));
        $response->assertForbidden();
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('content.index'));
        $response->assertRedirect(route('login'));
    }
}
