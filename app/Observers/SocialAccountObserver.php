<?php

namespace App\Observers;

use App\Models\SocialAccount;
use App\Services\QuotaService;

class SocialAccountObserver
{
    public function __construct(private QuotaService $quota) {}

    public function created(SocialAccount $account): void
    {
        $account->agency?->increment('social_accounts_count');
    }

    public function deleted(SocialAccount $account): void
    {
        $account->agency?->decrement('social_accounts_count');
    }
}
