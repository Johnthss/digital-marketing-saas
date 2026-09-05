<?php

namespace App\Observers;

use App\Models\AiContentLog;
use App\Services\QuotaService;

class AiContentLogObserver
{
    public function __construct(private QuotaService $quota) {}

    public function created(AiContentLog $log): void
    {
        $agency = $log->agency;
        if ($agency && $log->status === 'success') {
            $this->quota->incrementAiGenerationCount($agency);
        }
        $agency?->increment('ai_requests_count');
    }
}
