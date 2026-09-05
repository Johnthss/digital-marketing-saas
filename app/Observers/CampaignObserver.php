<?php

namespace App\Observers;

use App\Models\Campaign;
use App\Services\QuotaService;

class CampaignObserver
{
    public function __construct(private QuotaService $quota) {}

    public function created(Campaign $campaign): void
    {
        $campaign->agency?->increment('campaigns_count');
    }

    public function deleted(Campaign $campaign): void
    {
        $campaign->agency?->decrement('campaigns_count');
    }
}
