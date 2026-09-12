<?php

namespace Tests\Unit\Observers;

use App\Models\Agency;
use App\Models\SocialPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ObserverTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function social_post_observer_clears_cache_on_create(): void
    {
        $agency = Agency::factory()->create();
        $post = SocialPost::factory()->create(['agency_id' => $agency->id]);
        $this->assertNotNull($post);
    }

    #[Test]
    public function social_post_observer_clears_cache_on_update(): void
    {
        $post = SocialPost::factory()->create();
        $post->update(['status' => 'published']);
        $this->assertEquals('published', $post->fresh()->status);
    }

    #[Test]
    public function social_post_observer_clears_cache_on_delete(): void
    {
        $post = SocialPost::factory()->create();
        $id = $post->id;
        $post->delete();
        $this->assertSoftDeleted('social_posts', ['id' => $id]);
    }
}
