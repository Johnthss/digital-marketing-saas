<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\PostPublished;
use App\Events\PostFailed;
use App\Events\PostScheduled;
use App\Events\CampaignStatusChanged;
use App\Events\ClientCreated;
use App\Events\InvoicePaid;
use App\Events\AiGenerationCompleted;
use App\Events\SubscriptionUpgraded;
use App\Listeners\Social\ClearPostCache;
use App\Listeners\Social\LogPostActivity;
use App\Listeners\Social\SendPostNotification;
use App\Listeners\Billing\LogInvoiceActivity;
use App\Listeners\Billing\LogSubscriptionUpgrade;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PostPublished::class => [
            ClearPostCache::class . '@handlePostPublished',
            LogPostActivity::class . '@handlePostPublished',
            SendPostNotification::class . '@handlePostPublished',
        ],
        PostScheduled::class => [
            ClearPostCache::class . '@handlePostScheduled',
            LogPostActivity::class . '@handlePostScheduled',
        ],
        PostFailed::class => [
            ClearPostCache::class . '@handlePostFailed',
            LogPostActivity::class . '@handlePostFailed',
            SendPostNotification::class . '@handlePostFailed',
        ],
        CampaignStatusChanged::class => [
            // LogCampaignActivity
        ],
        ClientCreated::class => [
            // LogClientActivity
        ],
        InvoicePaid::class => [
            LogInvoiceActivity::class . '@handle',
        ],
        SubscriptionUpgraded::class => [
            LogSubscriptionUpgrade::class . '@handle',
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
