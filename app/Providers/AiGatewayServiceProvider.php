<?php

namespace App\Providers;

use App\Services\AI\Gateway\AiGateway;
use App\Services\AI\Gateway\Providers\OpenAiProvider;
use App\Services\AI\Gateway\Providers\AnthropicProvider;
use App\Services\AI\Gateway\Providers\GoogleProvider;
use Illuminate\Support\ServiceProvider;

class AiGatewayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AiGateway::class, function ($app) {
            $gateway = new AiGateway();

            // Register providers
            $openAi = new OpenAiProvider();
            if ($openAi->isAvailable()) {
                $gateway->registerProvider('openai', $openAi);
            }

            $anthropic = new AnthropicProvider();
            if ($anthropic->isAvailable()) {
                $gateway->registerProvider('anthropic', $anthropic);
            }

            $google = new GoogleProvider();
            if ($google->isAvailable()) {
                $gateway->registerProvider('google', $google);
            }

            return $gateway;
        });
    }

    public function boot(): void
    {
        //
    }
}
