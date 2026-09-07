<?php

namespace Tests\Feature\Api;

use App\Models\Agency;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSocialPostTest extends TestCase
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

    public function test_it_lists_posts_for_authenticated_agency(): void
    {
        SocialPost::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->getJson('/api/v1/posts');
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    public function test_it_filters_posts_by_status(): void
    {
        SocialPost::factory()->create(['agency_id' => $this->agency->id, 'status' => 'published']);
        SocialPost::factory()->create(['agency_id' => $this->agency->id, 'status' => 'draft']);
        $response = $this->actingAs($this->user)->getJson('/api/v1/posts?status=published');
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_it_creates_a_post(): void
    {
        $account = SocialAccount::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->postJson('/api/v1/posts', [
            'platform' => 'twitter',
            'content' => 'Test post',
            'social_account_id' => $account->id,
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('social_posts', ['content' => 'Test post']);
    }

    public function test_it_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/posts', [
            'platform' => 'invalid_platform',
        ]);
        $response->assertUnprocessable();
    }

    public function test_it_shows_a_post(): void
    {
        $post = SocialPost::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->getJson("/api/v1/posts/{$post->id}");
        $response->assertOk();
        $response->assertJsonPath('data.id', $post->id);
    }

    public function test_it_updates_a_post(): void
    {
        $post = SocialPost::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->putJson("/api/v1/posts/{$post->id}", [
            'content' => 'Updated content',
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('social_posts', ['id' => $post->id, 'content' => 'Updated content']);
    }

    public function test_it_deletes_a_post(): void
    {
        $post = SocialPost::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->deleteJson("/api/v1/posts/{$post->id}");
        $response->assertNoContent();
        $this->assertSoftDeleted('social_posts', ['id' => $post->id]);
    }

    public function test_it_prevents_access_to_other_agency_posts(): void
    {
        $otherAgency = Agency::factory()->create();
        $post = SocialPost::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->getJson("/api/v1/posts/{$post->id}");
        $response->assertNotFound();
    }
}
