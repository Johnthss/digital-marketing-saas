<?php

namespace Tests\Feature\Social;

use App\Models\Agency;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialPostTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private SocialAccount $account;

    protected function setUp(): void
    {
        parent::setUp();
        $agency = Agency::factory()->create(['subscription_plan' => 'starter']);
        $this->user = User::factory()->create(['agency_id' => $agency->id, 'role' => 'owner']);
        $this->account = SocialAccount::factory()->create(['agency_id' => $agency->id]);
    }

    public function test_posts_index_requires_authentication(): void
    {
        $response = $this->get('/social/posts');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_posts(): void
    {
        $response = $this->actingAs($this->user)->get('/social/posts');
        $response->assertStatus(200);
    }

    public function test_user_can_create_post(): void
    {
        $response = $this->actingAs($this->user)->post('/social/posts', [
            'social_account_id' => $this->account->id,
            'content' => 'Test post content',
        ]);

        $response->assertRedirect('/social/posts');
        $this->assertDatabaseHas('social_posts', ['content' => 'Test post content']);
    }

    public function test_user_cannot_create_post_for_other_agency_account(): void
    {
        $otherAccount = SocialAccount::factory()->create();
        $response = $this->actingAs($this->user)->post('/social/posts', [
            'social_account_id' => $otherAccount->id,
            'content' => 'Test post content',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_view_post(): void
    {
        $post = SocialPost::factory()->create([
            'agency_id' => $this->user->agency_id,
            'social_account_id' => $this->account->id,
        ]);

        $response = $this->actingAs($this->user)->get("/social/posts/{$post->id}");
        $response->assertStatus(200);
    }

    public function test_user_cannot_view_other_agency_post(): void
    {
        $post = SocialPost::factory()->create();
        $response = $this->actingAs($this->user)->get("/social/posts/{$post->id}");
        $response->assertStatus(403);
    }

    public function test_user_can_delete_post(): void
    {
        $post = SocialPost::factory()->create([
            'agency_id' => $this->user->agency_id,
            'social_account_id' => $this->account->id,
        ]);

        $response = $this->actingAs($this->user)->delete("/social/posts/{$post->id}");
        $response->assertRedirect('/social/posts');
        $this->assertSoftDeleted('social_posts', ['id' => $post->id]);
    }
}
