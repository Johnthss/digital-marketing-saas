<?php

namespace App\Services\Workflow;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\Workflow;
use App\Models\WorkflowExecution;
use App\Services\AI\AiContentService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorkflowEngine
{
    public function __construct(private AiContentService $aiContent) {}

    public function execute(Workflow $workflow, array $triggerData = []): WorkflowExecution
    {
        $execution = WorkflowExecution::create([
            'workflow_id' => $workflow->id,
            'status' => 'running',
            'trigger_data' => $triggerData,
            'started_at' => now(),
        ]);

        $actionResults = [];
        $startTime = microtime(true);

        try {
            if (! $this->evaluateConditions($workflow->conditions, $triggerData)) {
                $execution->update([
                    'status' => 'success',
                    'action_results' => ['skipped' => 'Conditions not met'],
                    'duration_ms' => $this->calcDuration($startTime),
                    'completed_at' => now(),
                ]);

                return $execution;
            }

            foreach ($workflow->actions ?? [] as $action) {
                $result = $this->executeAction($action, $triggerData, (int) $workflow->agency_id);
                $actionResults[] = $result;
            }

            $execution->update([
                'status' => 'success',
                'action_results' => $actionResults,
                'output_data' => $actionResults,
                'duration_ms' => $this->calcDuration($startTime),
                'completed_at' => now(),
            ]);

            $workflow->increment('execution_count');
            $workflow->update(['last_executed_at' => now()]);

        } catch (\Exception $e) {
            $execution->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'action_results' => $actionResults,
                'duration_ms' => $this->calcDuration($startTime),
                'completed_at' => now(),
            ]);
            $workflow->update(['error_message' => $e->getMessage()]);
            Log::error("Workflow #{$workflow->id} execution failed: {$e->getMessage()}");
        }

        return $execution;
    }

    protected function evaluateConditions(?array $conditions, array $triggerData): bool
    {
        if (empty($conditions)) {
            return true;
        }
        foreach ($conditions as $key => $expected) {
            if (data_get($triggerData, $key) !== $expected) {
                return false;
            }
        }

        return true;
    }

    protected function executeAction(array $action, array $triggerData, int $agencyId, int $depth = 0): array
    {
        $type = $action['type'] ?? 'unknown';
        $config = $action['config'] ?? [];

        return match ($type) {
            'send_notification' => $this->actionSendNotification($config, $triggerData),
            'create_post' => $this->actionCreatePost($config, $triggerData, $agencyId),
            'schedule_post' => $this->actionSchedulePost($config, $triggerData, $agencyId),
            'ai_generate' => $this->actionAiGenerate($config, $triggerData, $agencyId),
            'webhook' => $this->actionWebhook($config, $triggerData),
            'sleep' => $this->actionSleep($config),
            'loop' => $this->actionLoop($config, $triggerData, $agencyId, $depth),
            default => ['status' => 'skipped', 'reason' => "Unknown action type: {$type}"],
        };
    }

    protected function actionSendNotification(array $config, array $triggerData): array
    {
        $channel = $config['channel'] ?? 'log';
        $message = $config['message'] ?? 'Workflow notification';
        $recipient = $config['recipient'] ?? 'admin';

        try {
            switch ($channel) {
                case 'log':
                    Log::info("[Workflow Notification] To: {$recipient}, Message: {$message}");
                    break;
                case 'telegram':
                case 'email':
                default:
                    Log::info("[Workflow Notification] {$message}");
            }

            return ['status' => 'success', 'action' => 'send_notification', 'channel' => $channel];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    protected function actionCreatePost(array $config, array $triggerData, int $agencyId): array
    {
        $accountId = $config['social_account_id'] ?? null;
        $content = $config['content'] ?? $triggerData['content'] ?? null;

        if (! $accountId || ! $content) {
            return ['status' => 'failed', 'reason' => 'Missing account ID or content'];
        }

        $account = SocialAccount::where('agency_id', $agencyId)->find($accountId);
        if (! $account) {
            return ['status' => 'failed', 'reason' => 'Social account not found'];
        }

        $post = SocialPost::create([
            'agency_id' => $account->agency_id,
            'social_account_id' => $account->id,
            'content' => $content,
            'hashtags' => $config['hashtags'] ?? [],
            'status' => 'draft',
            'scheduled_at' => null,
        ]);

        return ['status' => 'success', 'action' => 'create_post', 'post_id' => $post->id];
    }

    protected function actionSchedulePost(array $config, array $triggerData, int $agencyId): array
    {
        $accountId = $config['social_account_id'] ?? null;
        $content = $config['content'] ?? $triggerData['content'] ?? null;
        $scheduledAt = $config['scheduled_at'] ?? null;

        if (! $accountId || ! $content || ! $scheduledAt) {
            return ['status' => 'failed', 'reason' => 'Missing required fields'];
        }

        $account = SocialAccount::where('agency_id', $agencyId)->find($accountId);
        if (! $account) {
            return ['status' => 'failed', 'reason' => 'Social account not found'];
        }

        $post = SocialPost::create([
            'agency_id' => $account->agency_id,
            'social_account_id' => $account->id,
            'content' => $content,
            'hashtags' => $config['hashtags'] ?? [],
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);

        return ['status' => 'success', 'action' => 'schedule_post', 'post_id' => $post->id, 'scheduled_at' => $scheduledAt];
    }

    protected function actionAiGenerate(array $config, array $triggerData, int $agencyId): array
    {
        $prompt = $config['prompt'] ?? $triggerData['prompt'] ?? null;
        $type = $config['generation_type'] ?? 'social_post';

        if (! $prompt) {
            return ['status' => 'failed', 'reason' => 'Missing prompt'];
        }

        $agency = \App\Models\Agency::find($agencyId);
        if (! $agency || ! $agency->canGenerateAiContent()) {
            return ['status' => 'failed', 'reason' => 'AI generation quota exceeded'];
        }

        $response = $this->aiContent->generate(
            agency: $agency,
            prompt: $prompt,
            contentType: $type,
            maxTokens: min(2048, max(64, (int) ($config['max_tokens'] ?? 1024))),
        );

        return ['status' => 'success', 'action' => 'ai_generate', 'type' => $type, 'content' => $response->content];
    }

    protected function actionWebhook(array $config, array $triggerData): array
    {
        $url = $config['url'] ?? null;
        $method = $config['method'] ?? 'POST';
        $payload = $config['payload'] ?? $triggerData;

        if (! $url) {
            return ['status' => 'failed', 'reason' => 'Missing webhook URL'];
        }

        $this->assertSafeWebhookUrl($url);
        $method = strtolower($method);
        if (! in_array($method, ['post', 'put', 'patch'], true)) {
            return ['status' => 'failed', 'reason' => 'Unsupported webhook method'];
        }

        try {
            $response = Http::connectTimeout(3)->timeout(10)->retry(2, 250)->{$method}($url, $payload);

            return [
                'status' => $response->successful() ? 'success' : 'failed',
                'action' => 'webhook',
                'http_status' => $response->status(),
            ];
        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    protected function actionSleep(array $config): array
    {
        $seconds = $config['seconds'] ?? 1;

        return ['status' => 'success', 'action' => 'sleep', 'seconds' => $seconds, 'message' => "Sleep action recorded ({$seconds}s). Use a delayed job for actual waiting."];
    }

    /**
     * Execute a loop action - repeats nested actions N times.
     */
    protected function actionLoop(array $config, array $triggerData, int $agencyId, int $depth): array
    {
        if ($depth >= 3) {
            return ['status' => 'failed', 'reason' => 'Maximum loop depth exceeded'];
        }
        $iterations = min(25, max(0, (int) ($config['iterations'] ?? 1)));
        $actions = $config['actions'] ?? [];
        $results = [];

        for ($i = 0; $i < $iterations; $i++) {
            foreach ($actions as $action) {
                $result = $this->executeAction($action, $triggerData, $agencyId, $depth + 1);
                $results[] = $result;
            }
        }

        return ['status' => 'success', 'action' => 'loop', 'iterations' => $iterations, 'results' => $results];
    }

    private function assertSafeWebhookUrl(string $url): void
    {
        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));
        if (($parts['scheme'] ?? '') !== 'https' || $host === '' || $host === 'localhost' || str_ends_with($host, '.local')) {
            throw new \InvalidArgumentException('Webhook URL must use HTTPS and a public host.');
        }

        $addresses = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : gethostbynamel($host);
        if ($addresses === false || $addresses === []) {
            throw new \InvalidArgumentException('Webhook host could not be resolved.');
        }
        foreach ($addresses as $address) {
            if (filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                throw new \InvalidArgumentException('Webhook URL resolves to a private or reserved address.');
            }
        }
    }

    protected function calcDuration(float $startTime): int
    {
        return (int) round((microtime(true) - $startTime) * 1000);
    }
}
