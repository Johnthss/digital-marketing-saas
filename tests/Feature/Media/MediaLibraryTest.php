<?php

namespace Tests\Feature\Media;

use App\Models\Agency;
use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
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
    public function it_lists_media_assets(): void
    {
        MediaAsset::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('media.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_shows_upload_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('media.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_deletes_media(): void
    {
        $asset = MediaAsset::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('media.destroy', $asset));
        $response->assertRedirect();
        $this->assertSoftDeleted('media_assets', ['id' => $asset->id]);
    }

    /** @test */
    public function it_prevents_access_to_other_agency_media(): void
    {
        $otherAgency = Agency::factory()->create();
        $asset = MediaAsset::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('media.show', $asset));
        $response->assertForbidden();
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('media.index'));
        $response->assertRedirect(route('login'));
    }
}
