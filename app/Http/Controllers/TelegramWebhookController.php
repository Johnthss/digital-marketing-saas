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
        $secret = (string) config('telegram.webhook.secret_token');
        if ($secret === '' || ! hash_equals($secret, (string) $request->header('X-Telegram-Bot-Api-Secret-Token'))) {
            return response()->json(['error' => 'Unauthorized webhook'], 401);
        }

        $update = $request->all();

        Log::debug('Telegram webhook received', [
            'update_id' => $update['update_id'] ?? null,
            'message_id' => $update['message']['message_id'] ?? null,
            'chat_id' => $update['message']['chat']['id'] ?? null,
        ]);

        $this->telegram->handleWebhook($update);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Set up the webhook URL.
     */
    public function setupWebhook(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'required|url|max:500',
        ]);

        $result = $this->telegram->setWebhook($validated['url']);

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
