<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Services\AI\AiContentService;
use App\Services\QuotaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiContentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;
        $quotaService = app(QuotaService::class);

        return view('ai.index', [
            'agency' => $agency,
            'remaining' => $quotaService->remainingAiGenerations($agency),
        ]);
    }

    public function generate(Request $request, AiContentService $service)
    {
        $agency = $request->user()->agency;

        $validated = $request->validate([
            'prompt' => 'required|string|max:2000',
            'content_type' => 'required|in:post,caption,hashtag,headline,email,ad_copy',
            'model' => 'nullable|string|max:100',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:50|max:8000',
        ]);

        $model = $validated['model'] ?? 'gpt-4o';
        $temperature = $validated['temperature'] ?? 0.7;
        $maxTokens = $validated['max_tokens'] ?? 2000;

        try {
            $response = $service->generate(
                agency: $agency,
                prompt: $validated['prompt'],
                contentType: $validated['content_type'],
                model: $model,
                temperature: (float) $temperature,
                maxTokens: (int) $maxTokens,
            );

            return response()->json([
                'success' => true,
                'content' => $response->content,
                'tokens' => $response->totalTokens,
                'cost' => $response->costUsd,
                'provider' => $response->provider,
                'model' => $response->model,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function rewrite(Request $request, AiContentService $service)
    {
        $agency = $request->user()->agency;

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'instructions' => 'nullable|string|max:1000',
        ]);

        try {
            $rewritten = $service->rewrite(
                agency: $agency,
                content: $validated['content'],
                instructions: $validated['instructions'] ?? null,
            );

            return response()->json([
                'success' => true,
                'content' => $rewritten,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function hashtags(Request $request, AiContentService $service)
    {
        $agency = $request->user()->agency;

        $validated = $request->validate([
            'topic' => 'required|string|max:500',
            'count' => 'nullable|integer|min:1|max:30',
            'platform' => 'nullable|string|max:50',
        ]);

        try {
            $hashtags = $service->generateHashtags(
                agency: $agency,
                topic: $validated['topic'],
                count: (int) ($validated['count'] ?? 10),
                platform: $validated['platform'] ?? null,
            );

            return response()->json([
                'success' => true,
                'hashtags' => $hashtags,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function ideas(Request $request, AiContentService $service)
    {
        $agency = $request->user()->agency;

        $validated = $request->validate([
            'topic' => 'required|string|max:500',
            'count' => 'nullable|integer|min:1|max:10',
            'platform' => 'nullable|string|max:50',
        ]);

        try {
            $ideas = $service->generateIdeas(
                agency: $agency,
                topic: $validated['topic'],
                count: (int) ($validated['count'] ?? 5),
                platform: $validated['platform'] ?? null,
            );

            return response()->json([
                'success' => true,
                'ideas' => $ideas,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
