<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\AiContentLog;
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

        // Get recent generations
        $recentGenerations = AiContentLog::where('agency_id', $agency->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('ai.index', [
            'agency' => $agency,
            'remaining' => $quotaService->remainingAiGenerations($agency),
            'recentGenerations' => $recentGenerations,
        ]);
    }

    public function generate(Request $request, AiContentService $service)
    {
        $agency = $request->user()->agency;

        $validated = $request->validate([
            'prompt' => 'required|string|max:2000',
            'content_type' => 'required|in:post,caption,hashtag,headline,email,ad_copy,landing_page,blog',
            'tone' => 'nullable|string|in:professional,casual,friendly,persuasive,informative,humorous',
            'length' => 'nullable|string|in:short,medium,long',
            'context' => 'nullable|string|max:1000',
        ]);

        // Build full prompt with tone and context
        $fullPrompt = $validated['prompt'];
        if (!empty($validated['tone'])) {
            $fullPrompt .= "\n\nTone: " . $validated['tone'];
        }
        if (!empty($validated['length'])) {
            $lengthMap = ['short' => '50-100 words', 'medium' => '150-250 words', 'long' => '300-500 words'];
            $fullPrompt .= "\n\nLength: " . ($lengthMap[$validated['length']] ?? 'medium');
        }
        if (!empty($validated['context'])) {
            $fullPrompt .= "\n\nAdditional context: " . $validated['context'];
        }

        try {
            $response = $service->generate(
                agency: $agency,
                prompt: $fullPrompt,
                contentType: $validated['content_type'],
            );

            // Return HTML view if not AJAX
            if (!$request->ajax() && !$request->wantsJson()) {
                $recentGenerations = AiContentLog::where('agency_id', $agency->id)
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();

                return view('ai.index', [
                    'agency' => $agency,
                    'remaining' => app(QuotaService::class)->remainingAiGenerations($agency),
                    'recentGenerations' => $recentGenerations,
                    'generatedContent' => $response->content,
                    'tokensUsed' => $response->totalTokens,
                    'costUsd' => $response->costUsd,
                ]);
            }

            return response()->json([
                'success' => true,
                'content' => $response->content,
                'tokens' => $response->totalTokens,
                'cost' => $response->costUsd,
                'provider' => $response->provider,
                'model' => $response->model,
            ]);
        } catch (\Exception $e) {
            if (!$request->ajax() && !$request->wantsJson()) {
                return back()->with('error', 'Generation failed: ' . $e->getMessage())->withInput();
            }

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
        ]);

        try {
            $ideas = $service->generateIdeas(
                agency: $agency,
                topic: $validated['topic'],
                count: (int) ($validated['count'] ?? 5),
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
