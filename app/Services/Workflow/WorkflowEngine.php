<?php

namespace App\Services\Workflow;

use App\Models\Workflow;
use App\Models\WorkflowExecution;
use Illuminate\Support\Facades\Log;

class WorkflowEngine
{
    /**
     * Execute a workflow with the given trigger data.
     */
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
            // Check conditions
            if (!$this->evaluateConditions($workflow->conditions, $triggerData)) {
                $execution->update([
                    'status' => 'success',
                    'action_results' => ['skipped' => 'Conditions not met'],
                    'duration_ms' => $this->calcDuration($startTime),
                    'completed_at' => now(),
                ]);
                return $execution;
            }

            // Execute actions
            foreach ($workflow->actions ?? [] as $action) {
                $result = $this->executeAction($action, $triggerData);
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

    /**
     * Evaluate conditions against trigger data.
     */
    protected function evaluateConditions(?array $conditions, array $triggerData): bool
    {
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $key => $expected) {
            $actual = data_get($triggerData, $key);
            if ($actual !== $expected) {
                return false;
            }
        }

        return true;
    }

    /**
     * Execute a single action.
     */
    protected function executeAction(array $action, array $triggerData): array
    {
        $type = $action['type'] ?? 'unknown';
        $config = $action['config'] ?? [];

        return match ($type) {
            'send_notification' => $this->actionSendNotification($config, $triggerData),
            'create_post' => $this->actionCreatePost($config, $triggerData),
            'schedule_post' => $this->actionSchedulePost($config, $triggerData),
            'ai_generate' => $this->actionAiGenerate($config, $triggerData),
            'webhook' => $this->actionWebhook($config, $triggerData),
            'sleep' => $this->actionSleep($config),
            default => ['status' => 'skipped', 'reason' => "Unknown action type: {$type}"],
        };
    }

    protected function actionSendNotification(array $config, array $triggerData): array
    {
        // TODO: Implement notification sending
        return ['status' => 'success', 'action' => 'send_notification', 'config' => $config];
    }

    protected function actionCreatePost(array $config, array $triggerData): array
    {
        // TODO: Implement post creation
        return ['status' => 'success', 'action' => 'create_post', 'config' => $config];
    }

    protected function actionSchedulePost(array $config, array $triggerData): array
    {
        // TODO: Implement post scheduling
        return ['status' => 'success', 'action' => 'schedule_post', 'config' => $config];
    }

    protected function actionAiGenerate(array $config, array $triggerData): array
    {
        // TODO: Implement AI generation
        return ['status' => 'success', 'action' => 'ai_generate', 'config' => $config];
    }

    protected function actionWebhook(array $config, array $triggerData): array
    {
        // TODO: Implement webhook call
        return ['status' => 'success', 'action' => 'webhook', 'config' => $config];
    }

    protected function actionSleep(array $config): array
    {
        $seconds = $config['seconds'] ?? 1;
        sleep(min($seconds, 10)); // Max 10 seconds
        return ['status' => 'success', 'action' => 'sleep', 'seconds' => $seconds];
    }

    protected function calcDuration(float $startTime): int
    {
        return (int) round((microtime(true) - $startTime) * 1000);
    }
}
