<?php

namespace App\Observers;

use App\Models\SocialPost;
use App\Services\Analytics\AnalyticsService;
use App\Services\QuotaService;
use Illuminate\Support\Facades\Log;

class SocialPostObserver
{
    public function __construct(
        private QuotaService $quota,
        private AnalyticsService $analytics
    ) {}

    public function created(SocialPost $post): void
    {
        $agency = $post->agency;
        if ($agency) {
            $this->quota->incrementPostCount($agency);
            $this->analytics->clearCache($agency);
        }
    }

    public function deleted(SocialPost $post): void
    {
        $agency = $post->agency;
        if ($agency) {
            $this->quota->decrementPostCount($agency);
            $this->analytics->clearCache($agency);
        }
    }

    public function updated(SocialPost $post): void
    {
        $agency = $post->agency;

        // Handle status changes
        if ($post->isDirty('status')) {
            $oldStatus = $post->getOriginal('status');
            $newStatus = $post->status;

            if ($oldStatus !== 'published' && $newStatus === 'published') {
                Log::info("Post #{$post->id} published");
            }
        }

        // Clear cache if relevant fields changed
        if ($post->isDirty(['status', 'engagement_rate', 'platform', 'agency_id'])) {
            if ($agency) {
                $this->analytics->clearCache($agency);
            }
        }
    }
}
