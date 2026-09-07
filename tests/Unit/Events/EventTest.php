<?php

namespace Tests\Unit\Events;

use App\Events\PostPublished;
use App\Models\SocialPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function post_published_event_can_be_instantiated(): void
    {
        $post = SocialPost::factory()->create();
        $event = new PostPublished($post);
        $this->assertInstanceOf(PostPublished::class, $event);
        $this->assertEquals($post->id, $event->post->id);
    }

    /** @test */
    public function event_broadcasts_on_channel(): void
    {
        $post = SocialPost::factory()->create();
        $event = new PostPublished($post);
        $this->assertNotEmpty($event->broadcastOn());
    }
}
