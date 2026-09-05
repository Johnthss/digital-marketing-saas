<?php

namespace App\Events;

use App\Models\SocialPost;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public SocialPost $post,
        public string $errorMessage,
        public int $attemptNumber = 1,
    ) {}
}
