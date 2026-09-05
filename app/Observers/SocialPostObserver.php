<?php

namespace App\Observers;

use App\Models\SocialPost;
use App\Models\Agency;
use App\Services\QuotaService;
use Illuminate\Support\Facades\Log;

class SocialPostObserver
{
    public function __construct(private QuotaService $quota) {}

    public function created(SocialPost $post): void
    {
        $agency = $post->agency;
        if ($agency) {
            $this->quota->incrementPostCount($agency);
        }
    }

    public function deleted(SocialPost $post): void
    {
        $agency = $post->agency;
        if ($agency) {
            $this->quota->decrementPostCount($agency);
        }
    }

    public function updated(SocialPost $post): void
    {
        // Handle status changes
        if ($post->isDirty('status')) {
            $oldStatus = $post->getOriginal('status');
            $newStatus = $post->status;

            if ($oldStatus !== 'published' && $newStatus === 'published') {
                // Post just published - could trigger events here
                Log::info("Post #{$post->id} published");
            }
        }
    }
}
