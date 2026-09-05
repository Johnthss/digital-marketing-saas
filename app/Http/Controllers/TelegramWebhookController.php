<?php

namespace App\Http\Controllers;

use App\Services\Telegram\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __construct(private TelegramBotService $telegram)
    {
        // No auth middleware - Telegram calls this directly
    }

    /**
     * Handle incoming webhook from Telegram.
     */
    public function handle(Request $request): JsonResponse
    {
        $update = $request->all();

        Log::debug('Telegram webhook received', $update);

        $this->telegram->handleWebhook($update);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Set up the webhook URL.
     */
    public function setupWebhook(Request $request): JsonResponse
    {
        $url = $request->input('url');

        if (!$url) {
            return response()->json(['error' => 'URL required'], 400);
        }

        $result = $this->telegram->setWebhook($url);

        return response()->json($result);
    }

    /**
     * Get webhook info.
     */
    public function webhookInfo(): JsonResponse
    {
        return response()->json($this->telegram->getWebhookInfo());
    }
}
