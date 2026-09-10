<?php

namespace Tests\Unit\Services\AI;

use App\Services\AI\Gateway\AiResponse;
use App\Services\AI\Gateway\Providers\GroqProvider;
use Tests\TestCase;

class GroqProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['platform.ai.providers.groq.api_key' => 'gsk-test-key']);
    }

    public function test_groq_provider_is_available_with_key(): void
    {
        $provider = new GroqProvider();

        $this->assertTrue($provider->isAvailable());
        $this->assertEquals('groq', $provider->getName());
        $this->assertEquals('Groq', $provider->getDisplayName());
    }

    public function test_groq_provider_returns_supported_models(): void
    {
        $provider = new GroqProvider();
        $models = $provider->getSupportedModels();

        $this->assertContains('llama-3.1-70b', $models);
        $this->assertContains('llama-3.1-8b', $models);
        $this->assertContains('mixtral-8x7b', $models);
        $this->assertContains('gemma2-9b', $models);
    }

    public function test_groq_provider_calculates_cost_correctly(): void
    {
        $provider = new GroqProvider();

        $response = new AiResponse(
            content: 'Fast LLM inference',
            model: 'llama-3.1-70b',
            provider: 'groq',
            promptTokens: 1000,
            completionTokens: 500,
            totalTokens: 1500,
            finishReason: 'stop',
        );

        $cost = $provider->calculateCost($response);

        // llama-3.1-70b: $0.59/1M input, $0.79/1M output
        // (1000/1000000 * 0.59) + (500/1000000 * 0.79) = 0.00059 + 0.000395 = 0.000985
        $this->assertEquals(0.000985, $cost);
    }
}
