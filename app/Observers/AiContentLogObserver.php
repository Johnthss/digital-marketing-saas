<?php

namespace App\Observers;

use App\Models\AiContentLog;
use App\Services\QuotaService;
use App\Services\Analytics\AnalyticsService;

class AiContentLogObserver
{
    public function __construct(
        private QuotaService $quota,
        private AnalyticsService $analytics
    ) {}

    public function created(AiContentLog $log): void
    {
        $agency = $log->agency;
        if ($agency && $log->status === 'success') {
            $this->quota->incrementAiGenerationCount($agency);
            $this->analytics->clearCache($agency);
        }
        $agency?->increment('ai_requests_count');
    }

    public function deleted(AiContentLog $log): void
    {
        $agency = $log->agency;
        if ($agency) {
            $this->analytics->clearCache($agency);
        }
    }
}
