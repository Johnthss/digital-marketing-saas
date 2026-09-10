<?php

namespace App\Services\AI\Gateway\Providers;

use App\Services\AI\Gateway\AiRequest;
use App\Services\AI\Gateway\AiResponse;
use App\Services\AI\Gateway\Contracts\AiProviderInterface;
use App\Services\AI\Gateway\Enums\FinishReason;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqProvider implements AiProviderInterface
{
    protected string $apiKey;

    protected string $apiBaseUrl;

    protected string $defaultModel;

    protected array $pricing = [
        'llama-3.1-70b' => ['input' => 0.59, 'output' => 0.79],
        'llama-3.1-8b' => ['input' => 0.05, 'output' => 0.08],
        'mixtral-8x7b' => ['input' => 0.24, 'output' => 0.24],
        'gemma2-9b' => ['input' => 0.20, 'output' => 0.20],
    ];

    public function __construct()
    {
        $this->apiKey = config('platform.ai.providers.groq.api_key');
        $this->apiBaseUrl = config('platform.ai.providers.groq.api_base_url', 'https://api.groq.com/openai/v1');
        $this->defaultModel = config('platform.ai.providers.groq.model', 'llama-3.1-70b');
    }

    public function send(AiRequest $request): AiResponse
    {
        $model = $request->model ?? $this->defaultModel;

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post("{$this->apiBaseUrl}/chat/completions", [
                    'model' => $model,
                    'messages' => [
                        ...($request->systemPrompt ? [['role' => 'system', 'content' => $request->systemPrompt]] : []),
                        ['role' => 'user', 'content' => $request->prompt],
                    ],
                    'temperature' => $request->temperature,
                    'max_tokens' => $request->maxTokens,
                ]);

            if ($response->failed()) {
                throw new \RuntimeException("Groq API error: {$response->status()} - {$response->body()}");
            }

            $data = $response->json();

            return new AiResponse(
                content: $data['choices'][0]['message']['content'] ?? '',
                model: $data['model'] ?? $model,
                provider: $this->getName(),
                promptTokens: $data['usage']['prompt_tokens'] ?? 0,
                completionTokens: $data['usage']['completion_tokens'] ?? 0,
                totalTokens: $data['usage']['total_tokens'] ?? 0,
                finishReason: match ($data['choices'][0]['finish_reason'] ?? '') {
                    'stop' => FinishReason::STOP->value,
                    'length' => FinishReason::LENGTH->value,
                    'content_filter' => FinishReason::CONTENT_FILTER->value,
                    default => FinishReason::OTHER->value,
                },
            );
        } catch (\Exception $e) {
            Log::error("Groq provider error: {$e->getMessage()}");
            throw $e;
        }
    }

    public function getName(): string
    {
        return 'groq';
    }

    public function getDisplayName(): string
    {
        return 'Groq';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->apiKey);
    }

    public function getSupportedModels(): array
    {
        return ['llama-3.1-70b', 'llama-3.1-8b', 'mixtral-8x7b', 'gemma2-9b'];
    }

    public function getDefaultModel(): string
    {
        return $this->defaultModel;
    }

    public function calculateCost(AiResponse $response): float
    {
        $model = $response->model;
        $pricing = $this->pricing[$model] ?? $this->pricing['llama-3.1-70b'];
        $inputCost = ($response->promptTokens / 1_000_000) * $pricing['input'];
        $outputCost = ($response->completionTokens / 1_000_000) * $pricing['output'];

        return round($inputCost + $outputCost, 6);
    }
}
