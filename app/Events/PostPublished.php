<?php

namespace App\Events;

use App\Models\SocialPost;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostPublished implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public SocialPost $post,
        public array $results = []
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('agency.'.$this->post->agency_id)];
    }
}
