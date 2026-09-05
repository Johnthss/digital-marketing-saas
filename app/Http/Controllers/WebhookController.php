<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;
        $webhooks = Webhook::where('agency_id', $agency->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('webhooks.index', compact('agency', 'webhooks'));
    }

    public function create(Request $request)
    {
        $agency = $request->user()->agency;
        $events = Webhook::$availableEvents;

        return view('webhooks.create', compact('agency', 'events'));
    }

    public function store(Request $request)
    {
        $agency = $request->user()->agency;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'events' => 'required|array|min:1',
            'events.*' => 'in:' . implode(',', array_keys(Webhook::$availableEvents)),
            'is_active' => 'boolean',
        ]);

        $webhook = Webhook::create([
            'agency_id' => $agency->id,
            'name' => $validated['name'],
            'url' => $validated['url'],
            'events' => $validated['events'],
            'secret' => Str::random(40),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('webhooks.show', $webhook)
            ->with('success', 'Webhook created successfully.');
    }

    public function show(Request $request, Webhook $webhook)
    {
        $agency = $request->user()->agency;

        if ($webhook->agency_id !== $agency->id) {
            abort(403);
        }

        $logs = $webhook->logs()->orderBy('created_at', 'desc')->paginate(25);

        return view('webhooks.show', compact('agency', 'webhook', 'logs'));
    }

    public function edit(Request $request, Webhook $webhook)
    {
        $agency = $request->user()->agency;

        if ($webhook->agency_id !== $agency->id) {
            abort(403);
        }

        $events = Webhook::$availableEvents;

        return view('webhooks.edit', compact('agency', 'webhook', 'events'));
    }

    public function update(Request $request, Webhook $webhook)
    {
        $agency = $request->user()->agency;

        if ($webhook->agency_id !== $agency->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'events' => 'required|array|min:1',
            'is_active' => 'boolean',
        ]);

        $webhook->update($validated);

        return redirect()->route('webhooks.show', $webhook)
            ->with('success', 'Webhook updated successfully.');
    }

    public function destroy(Request $request, Webhook $webhook)
    {
        $agency = $request->user()->agency;

        if ($webhook->agency_id !== $agency->id) {
            abort(403);
        }

        $webhook->delete();

        return redirect()->route('webhooks.index')
            ->with('success', 'Webhook deleted.');
    }

    /**
     * Trigger a webhook for a specific event.
     */
    public function trigger(string $event, array $payload = []): void
    {
        $webhooks = Webhook::whereJsonContains('events', $event)
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            $this->dispatchWebhook($webhook, $event, $payload);
        }
    }

    /**
     * Dispatch webhook to endpoint.
     */
    protected function dispatchWebhook(Webhook $webhook, string $event, array $payload): void
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => $webhook->content_type,
                    'X-Webhook-Event' => $event,
                    'X-Webhook-Signature' => $this->signPayload($payload, $webhook->secret),
                ])
                ->post($webhook->url, [
                    'event' => $event,
                    'data' => $payload,
                    'timestamp' => now()->toISOString(),
                ]);

            WebhookLog::create([
                'webhook_id' => $webhook->webhook_id,
                'event' => $event,
                'status_code' => $response->status(),
                'payload' => $payload,
                'response' => $response->body(),
                'response_time_ms' => (int) round((microtime(true) - $startTime) * 1000),
                'is_success' => $response->successful(),
            ]);

            $webhook->increment('total_calls');

            if (!$response->successful()) {
                $webhook->increment('failed_calls');
            }
        } catch (\Exception $e) {
            WebhookLog::create([
                'webhook_id' => $webhook->webhook_id,
                'event' => $event,
                'payload' => $payload,
                'error_message' => $e->getMessage(),
                'response_time_ms' => (int) round((microtime(true) - $startTime) * 1000),
                'is_success' => false,
            ]);

            $webhook->increment('total_calls');
            $webhook->increment('failed_calls');
            Log::error("Webhook #{$webhook->id} failed: {$e->getMessage()}");
        }

        $webhook->update(['last_triggered_at' => now()]);
    }

    /**
     * Sign payload with HMAC-SHA256.
     */
    protected function signPayload(array $payload, string $secret): string
    {
        return hash_hmac('sha256', json_encode($payload), $secret);
    }
}
