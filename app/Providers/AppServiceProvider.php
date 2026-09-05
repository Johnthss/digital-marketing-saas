<?php

namespace App\Providers;

use App\Services\ActivityLogService;
use App\Services\QuotaService;
use App\Services\AI\AiContentService;
use App\Services\AI\AiGateway;
use App\Services\AI\AgencyAIAssistantService;
use App\Services\Telegram\TelegramBotService;
use App\Services\SocialPostService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QuotaService::class);
        $this->app->singleton(ActivityLogService::class);
        $this->app->singleton(AiContentService::class);
        $this->app->singleton(AiGateway::class);
        $this->app->singleton(AgencyAIAssistantService::class);
        $this->app->singleton(TelegramBotService::class);
        $this->app->singleton(\App\Services\SocialPostService::class);
    }

    public function boot(): void
    {
        //
    }
}
