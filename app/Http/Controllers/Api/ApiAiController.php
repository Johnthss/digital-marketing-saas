<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AI\AiContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAiController extends Controller
{
    public function __construct(private AiContentService $aiService)
    {
        $this->middleware(['auth', 'agency']);
    }

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:generate,rewrite,summarize,translate,hashtags,ideas',
            'prompt' => 'required|string|max:10000',
            'content_type' => 'nullable|in:post,email,article,caption,hashtags,ideas',
            'tone' => 'nullable|string|in:professional,casual,friendly,formal,enthusiastic',
            'length' => 'nullable|in:short,medium,long',
        ]);

        $agency = $request->user()->agency;
        $result = $this->aiService->generate(
            agency: $agency,
            prompt: $request->prompt,
            contentType: $request->get('content_type', 'post'),
            tone: $request->get('tone'),
            length: $request->get('length'),
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
