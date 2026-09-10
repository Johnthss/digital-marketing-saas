<?php

namespace App\Http\Controllers;

use App\Jobs\RunAgentWorkflowJob;
use App\Models\AgentCostLog;
use App\Models\AgentWorkflowExecution;
use App\Services\AI\Agent\AgentContext;
use App\Services\AI\Agent\AgentCostTracker;
use App\Services\AI\Agent\AgentHealthMonitor;
use App\Services\AI\Agent\AgentOrchestrator;
use App\Services\AI\Agent\AgentTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function __construct(
        private AgentOrchestrator $orchestrator,
        private AgentHealthMonitor $healthMonitor,
        private AgentCostTracker $costTracker,
    ) {
        $this->middleware(['auth', 'agency']);
    }

    /**
     * Agent dashboard page with agent cards, health score, costs, and activity.
     */
    public function dashboard(Request $request)
    {
        $agency = $request->user()->agency;
        $agencyId = $request->user()->agency_id;

        // Get agent stats from orchestrator
        $agentStats = $this->orchestrator->getAgentStats();

        // Enhance with health data and status
        $agents = [];
        foreach ($agentStats as $name => $stat) {
            $agent = $this->orchestrator->getAgent($name);
            $health = $agent ? $this->healthMonitor->checkAgentHealth($agent) : null;
            
            $agents[$name] = array_merge($stat, [
                'status' => $health['status'] ?? 'active',
                'last_run' => $health['last_execution'] ?? 'Never',
                'description' => $this->getAgentDescription($name),
                'category' => $this->getAgentCategory($name),
            ]);
        }

        // System health score
        $agentHealth = $this->healthMonitor->getSystemHealth();

        // Cost summary
        $costSummary = [
            'total' => $this->costTracker->getMonthlyCost($agencyId),
            'by_agent' => $this->costTracker->getCostByAgent($agencyId),
        ];

        // Budget info
        $budgetLimit = $this->costTracker->getBudgetLimit($agencyId);
        $budgetRemaining = $this->costTracker->getRemainingBudget($agencyId);

        // Recent activity (last 10 agent executions)
        $recentActivity = AgentCostLog::byAgency($agencyId)
            ->orderBy('executed_at', 'desc')
            ->take(10)
            ->get();

        return view('agents.dashboard', compact(
            'agents',
            'agentHealth',
            'costSummary',
            'budgetLimit',
            'budgetRemaining',
            'recentActivity',
        ));
    }

    /**
     * Single agent detail page.
     */
    public function agentDetail(Request $request, string $agentName)
    {
        $agencyId = $request->user()->agency_id;

        $agents = $this->orchestrator->getAgentStats();

        if (!isset($agents[$agentName])) {
            abort(404);
        }

        $agent = $agents[$agentName];
        $agentObj = $this->orchestrator->getAgent($agentName);

        // Health check for status
        $health = $agentObj ? $this->healthMonitor->checkAgentHealth($agentObj) : null;
        $agent['status'] = $health['status'] ?? 'active';
        $agent['description'] = $this->getAgentDescription($agentName);
        $agent['category'] = $this->getAgentCategory($agentName);

        // Learned patterns from storage
        $learnedPatterns = [];
        $memoryPath = "agent_memory/global/{$agentName}.json";
        try {
            if (\Illuminate\Support\Facades\Storage::exists($memoryPath)) {
                $data = json_decode(\Illuminate\Support\Facades\Storage::get($memoryPath), true);
                $patterns = $data['stats'] ?? [];
                foreach ($patterns as $key => $value) {
                    if (is_array($value) && !empty($value)) {
                        $learnedPatterns[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . count($value) . ' samples collected';
                    }
                }
            }
        } catch (\Exception $e) {
            // Memory not available
        }

        // Recent executions for this agent
        $executions = AgentCostLog::byAgency($agencyId)
            ->byAgent($agentName)
            ->orderBy('executed_at', 'desc')
            ->take(20)
            ->get();

        return view('agents.show', compact('agent', 'agentName', 'learnedPatterns', 'executions'));
    }

    /**
     * Workflows page.
     */
    public function workflows(Request $request)
    {
        $agencyId = $request->user()->agency_id;

        // Get workflow data
        $workflows = $this->listWorkflows()->getData(true)['data'];

        // Execution history
        $executions = AgentWorkflowExecution::where('agency_id', $agencyId)
            ->orderBy('started_at', 'desc')
            ->take(50)
            ->get();

        return view('agents.workflows', compact('workflows', 'executions'));
    }

    /**
     * Get a human-readable description for an agent.
     */
    private function getAgentDescription(string $name): string
    {
        return match($name) {
            'content_agent' => 'Creates and optimizes content for social media, blogs, and marketing campaigns.',
            'analytics_agent' => 'Analyzes performance data, detects trends, and provides strategic recommendations.',
            'security_agent' => 'Performs security audits and vulnerability scans to protect agency assets.',
            'campaign_agent' => 'Optimizes marketing campaigns, allocates budgets, and designs A/B tests.',
            'social_media_agent' => 'Manages post scheduling, engagement analysis, and reply suggestions.',
            'support_agent' => 'Classifies support tickets, suggests responses, and detects escalations.',
            default => 'An intelligent AI agent handling specialized tasks.',
        };
    }

    /**
     * Get the category for an agent.
     */
    private function getAgentCategory(string $name): string
    {
        return match($name) {
            'content_agent', 'campaign_agent', 'social_media_agent' => 'Marketing',
            'analytics_agent' => 'Analytics',
            'security_agent' => 'Security',
            'support_agent' => 'Support',
            default => 'General',
        };
    }

    /**
     * List all registered agents with stats.
     */
    public function index(Request $request): JsonResponse
    {
        $agents = $this->orchestrator->getAgentStats();

        return response()->json([
            'success' => true,
            'data' => $agents,
        ]);
    }

    /**
     * Show agent details and learned patterns.
     */
    public function show(Request $request, string $agentName): JsonResponse
    {
        $agents = $this->orchestrator->getAgentStats();

        if (! isset($agents[$agentName])) {
            return response()->json([
                'success' => false,
                'message' => "Agent '{$agentName}' not found",
            ], 404);
        }

        $agentData = $agents[$agentName];

        // Get learned patterns from storage if available
        $memoryPath = "agent_memory/global/{$agentName}.json";
        $learnedPatterns = [];

        try {
            if (\Illuminate\Support\Facades\Storage::exists($memoryPath)) {
                $data = json_decode(\Illuminate\Support\Facades\Storage::get($memoryPath), true);
                $learnedPatterns = $data['stats'] ?? [];
            }
        } catch (\Exception $e) {
            // Memory not available, continue without it
        }

        return response()->json([
            'success' => true,
            'data' => [
                'agent' => $agentData,
                'learned_patterns' => $learnedPatterns,
                'registered_at' => $agentData['total_executed'] > 0 ? 'active' : 'idle',
            ],
        ]);
    }

    /**
     * Manually dispatch a task to an agent.
     */
    public function dispatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'agent_name' => 'required|string',
            'task_type' => 'required|string|in:content_generate,content_optimize,content_rewrite,hashtag_generate,performance_analysis,trend_detection,competitor_analysis,recommendation,security_audit,input_scan,auth_check,vulnerability_scan',
            'prompt' => 'required|string|max:10000',
            'data' => 'nullable|array',
        ]);

        $task = new AgentTask(
            id: uniqid('task_', true),
            type: $validated['task_type'],
            prompt: $validated['prompt'],
            data: $validated['data'] ?? [],
            preferredAgent: $validated['agent_name'],
        );

        $context = AgentContext::fromUser($request->user());
        $result = $this->orchestrator->dispatch($task, $context);

        if (! $result->success) {
            return response()->json([
                'success' => false,
                'message' => $result->error ?? 'Task failed',
                'agent' => $result->agentName,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result->toArray(),
        ]);
    }

    /**
     * Overall orchestration statistics.
     */
    public function stats(Request $request): JsonResponse
    {
        $agents = $this->orchestrator->getAgentStats();

        $totalExecuted = 0;
        $totalCost = 0.0;
        $totalSuccesses = 0;

        foreach ($agents as $agent) {
            $totalExecuted += $agent['total_executed'];
            $totalCost += $agent['total_cost'];
            $totalSuccesses += $agent['total_successes'];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_agents' => count($agents),
                'total_executed' => $totalExecuted,
                'total_successes' => $totalSuccesses,
                'overall_success_rate' => $totalExecuted > 0
                    ? round($totalSuccesses / $totalExecuted, 4)
                    : 0.0,
                'total_cost_usd' => round($totalCost, 6),
                'agents' => $agents,
            ],
        ]);
    }

    /**
     * Execute a named workflow.
     */
    public function runWorkflow(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'workflow_name' => 'required|string|in:content_campaign,competitor_analysis,security_audit,content_optimization,trend_report',
            'input' => 'sometimes|array',
            'async' => 'sometimes|boolean',
        ]);

        $workflowName = $validated['workflow_name'];
        $input = $validated['input'] ?? [];
        $async = $validated['async'] ?? true;

        $executionId = 'wf_' . uniqid();
        $agencyId = $request->user()->agency_id;
        $userId = $request->user()->id;

        // Create execution record
        $execution = AgentWorkflowExecution::create([
            'execution_id' => $executionId,
            'workflow_name' => $workflowName,
            'agency_id' => $agencyId,
            'user_id' => $userId,
            'status' => 'pending',
            'input_data' => $input,
            'steps_total' => 3,
            'steps_completed' => 0,
            'started_at' => now(),
        ]);

        if ($async) {
            RunAgentWorkflowJob::dispatch(
                workflowName: $workflowName,
                executionId: $executionId,
                agencyId: $agencyId,
                userId: $userId,
                input: $input,
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'execution_id' => $executionId,
                    'workflow_name' => $workflowName,
                    'status' => 'pending',
                    'message' => 'Workflow dispatched for async execution.',
                ],
            ], 202);
        }

        // Synchronous execution
        $context = AgentContext::fromUser($request->user());
        $tasks = $this->buildWorkflowTasks($workflowName, $input, $executionId);

        $results = $this->orchestrator->dispatchWorkflow($tasks, $context);

        $allSuccess = collect($results)->every(fn ($r) => $r->success);
        $execution->update([
            'status' => $allSuccess ? 'success' : 'failed',
            'steps_completed' => count($results),
            'output_data' => [
                'results' => array_map(fn ($r) => $r->toArray(), $results),
            ],
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'execution_id' => $executionId,
                'workflow_name' => $workflowName,
                'status' => $allSuccess ? 'success' : 'failed',
                'results' => array_map(fn ($r) => $r->toArray(), $results),
            ],
        ]);
    }

    /**
     * List available workflows with required features.
     */
    public function listWorkflows(): JsonResponse
    {
        $workflows = [
            [
                'name' => 'content_campaign',
                'description' => 'Generate a full content campaign with posts, hashtags, and scheduling recommendations.',
                'required_features' => ['ai_content', 'social_posting'],
                'steps' => ['content_generate', 'hashtag_generate', 'performance_analysis'],
            ],
            [
                'name' => 'competitor_analysis',
                'description' => 'Analyze competitors and generate strategic recommendations.',
                'required_features' => ['analytics', 'ai_content'],
                'steps' => ['competitor_analysis', 'trend_detection', 'recommendation'],
            ],
            [
                'name' => 'security_audit',
                'description' => 'Run a comprehensive security audit on accounts and content.',
                'required_features' => ['security'],
                'steps' => ['security_audit', 'vulnerability_scan', 'recommendation'],
            ],
            [
                'name' => 'content_optimization',
                'description' => 'Optimize existing content for better engagement.',
                'required_features' => ['ai_content'],
                'steps' => ['content_optimize', 'content_rewrite', 'hashtag_generate'],
            ],
            [
                'name' => 'trend_report',
                'description' => 'Generate a trend analysis report with actionable insights.',
                'required_features' => ['analytics'],
                'steps' => ['trend_detection', 'performance_analysis', 'recommendation'],
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $workflows,
        ]);
    }

    /**
     * Check workflow progress.
     */
    public function getWorkflowStatus(string $executionId): JsonResponse
    {
        $execution = AgentWorkflowExecution::where('execution_id', $executionId)->first();

        if ($execution === null) {
            return response()->json([
                'success' => false,
                'message' => "Workflow execution [{$executionId}] not found.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'execution_id' => $execution->execution_id,
                'workflow_name' => $execution->workflow_name,
                'status' => $execution->status,
                'steps_total' => $execution->steps_total,
                'steps_completed' => $execution->steps_completed,
                'progress_percentage' => $execution->steps_total > 0
                    ? round(($execution->steps_completed / $execution->steps_total) * 100, 1)
                    : 0,
                'input_data' => $execution->input_data,
                'output_data' => $execution->output_data,
                'error_message' => $execution->error_message,
                'started_at' => $execution->started_at?->toIso8601String(),
                'completed_at' => $execution->completed_at?->toIso8601String(),
                'duration_ms' => $execution->duration_ms,
            ],
        ]);
    }

    /**
     * Build AgentTask objects for a workflow.
     *
     * @return array<AgentTask>
     */
    private function buildWorkflowTasks(string $workflowName, array $input, string $executionId): array
    {
        $workflows = [
            'content_campaign' => [
                ['type' => 'content_generate', 'prompt' => 'Generate campaign content based on input'],
                ['type' => 'hashtag_generate', 'prompt' => 'Generate relevant hashtags for the content'],
                ['type' => 'performance_analysis', 'prompt' => 'Analyze predicted performance'],
            ],
            'competitor_analysis' => [
                ['type' => 'competitor_analysis', 'prompt' => 'Analyze competitor data'],
                ['type' => 'trend_detection', 'prompt' => 'Detect market trends'],
                ['type' => 'recommendation', 'prompt' => 'Generate strategic recommendations'],
            ],
            'security_audit' => [
                ['type' => 'security_audit', 'prompt' => 'Perform security audit'],
                ['type' => 'vulnerability_scan', 'prompt' => 'Scan for vulnerabilities'],
                ['type' => 'recommendation', 'prompt' => 'Generate security recommendations'],
            ],
            'content_optimization' => [
                ['type' => 'content_optimize', 'prompt' => 'Optimize content for engagement'],
                ['type' => 'content_rewrite', 'prompt' => 'Rewrite weak sections'],
                ['type' => 'hashtag_generate', 'prompt' => 'Generate optimized hashtags'],
            ],
            'trend_report' => [
                ['type' => 'trend_detection', 'prompt' => 'Detect current trends'],
                ['type' => 'performance_analysis', 'prompt' => 'Analyze trend performance'],
                ['type' => 'recommendation', 'prompt' => 'Generate trend-based recommendations'],
            ],
        ];

        $steps = $workflows[$workflowName] ?? [];
        $tasks = [];

        foreach ($steps as $index => $step) {
            $tasks[] = new AgentTask(
                id: "{$executionId}_step_{$index}",
                type: $step['type'],
                prompt: $step['prompt'],
                data: $input,
                metadata: [
                    'execution_id' => $executionId,
                    'step_index' => $index,
                    'workflow_name' => $workflowName,
                ],
            );
        }

        return $tasks;
    }
}
