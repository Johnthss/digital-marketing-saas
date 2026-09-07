<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Workflow;
use App\Models\WorkflowTemplate;
use App\Enums\WorkflowStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;

        $query = Workflow::where('agency_id', $agency->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $workflows = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('workflows.index', compact('agency', 'workflows'));
    }

    public function builder()
    {
        $templates = WorkflowTemplate::active()->public()->orderBy('category')->get();
        return view('workflows.builder', compact('templates'));
    }

    public function create(Request $request)
    {
        $agency = $request->user()->agency;
        $triggerTypes = Workflow::TRIGGER_TYPES;
        $actionTypes = Workflow::ACTION_TYPES;

        return view('workflows.create', compact('agency', 'triggerTypes', 'actionTypes'));
    }

    public function store(Request $request)
    {
        $agency = $request->user()->agency;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_type' => 'required|in:' . implode(',', array_keys(Workflow::TRIGGER_TYPES)),
            'trigger_config' => 'nullable|array',
            'actions' => 'required|array|min:1',
            'conditions' => 'nullable|array',
        ]);

        $workflow = Workflow::create([
            'agency_id' => $agency->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'trigger_type' => $validated['trigger_type'],
            'trigger_config' => $validated['trigger_config'] ?? [],
            'actions' => $validated['actions'],
            'conditions' => $validated['conditions'] ?? [],
            'status' => WorkflowStatus::DRAFT->value,
        ]);

        // Create initial version
        $workflow->createVersion('Initial version', Auth::id());

        return redirect()->route('workflows.show', $workflow)
            ->with('success', 'Workflow created successfully.');
    }

    public function show(Request $request, Workflow $workflow)
    {
        $agency = $request->user()->agency;

        if ($workflow->agency_id !== $agency->id) {
            abort(403);
        }

        $executions = $workflow->executions()->orderBy('started_at', 'desc')->paginate(10);
        $versions = $workflow->versions()->orderBy('version_number', 'desc')->paginate(10);
        $webhookLogs = $workflow->webhookLogs()->orderBy('created_at', 'desc')->paginate(10);

        return view('workflows.show', compact('agency', 'workflow', 'executions', 'versions', 'webhookLogs'));
    }

    public function edit(Request $request, Workflow $workflow)
    {
        $agency = $request->user()->agency;

        if ($workflow->agency_id !== $agency->id) {
            abort(403);
        }

        $triggerTypes = Workflow::TRIGGER_TYPES;
        $actionTypes = Workflow::ACTION_TYPES;

        return view('workflows.edit', compact('agency', 'workflow', 'triggerTypes', 'actionTypes'));
    }

    public function update(Request $request, Workflow $workflow)
    {
        $agency = $request->user()->agency;

        if ($workflow->agency_id !== $agency->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_type' => 'required|in:' . implode(',', array_keys(Workflow::TRIGGER_TYPES)),
            'trigger_config' => 'nullable|array',
            'actions' => 'required|array|min:1',
            'conditions' => 'nullable|array',
            'change_notes' => 'nullable|string|max:500',
        ]);

        $workflow->update([
            'name' => $validated['name'],
            'trigger_type' => $validated['trigger_type'],
            'trigger_config' => $validated['trigger_config'] ?? [],
            'actions' => $validated['actions'],
            'conditions' => $validated['conditions'] ?? [],
        ]);

        // Create version snapshot
        $workflow->createVersion($validated['change_notes'] ?? 'Updated workflow', Auth::id());

        return redirect()->route('workflows.show', $workflow)
            ->with('success', 'Workflow updated successfully.');
    }

    public function destroy(Request $request, Workflow $workflow)
    {
        $agency = $request->user()->agency;

        if ($workflow->agency_id !== $agency->id) {
            abort(403);
        }

        $workflow->delete();

        return redirect()->route('workflows.index')
            ->with('success', 'Workflow deleted.');
    }

    public function toggleStatus(Request $request, Workflow $workflow)
    {
        $agency = $request->user()->agency;

        if ($workflow->agency_id !== $agency->id) {
            abort(403);
        }

        $newStatus = $workflow->status === WorkflowStatus::ACTIVE->value
            ? WorkflowStatus::PAUSED->value
            : WorkflowStatus::ACTIVE->value;

        $workflow->update(['status' => $newStatus]);

        return back()->with('success', 'Workflow status updated.');
    }

    /**
     * Store workflow from visual builder.
     */
    public function storeFromBuilder(Request $request)
    {
        $agency = $request->user()->agency;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nodes' => 'required|array|min:1',
            'connections' => 'nullable|array',
        ]);

        // Extract trigger and actions from nodes
        $triggerNode = collect($validated['nodes'])->firstWhere('type', 'trigger');
        $actionNodes = collect($validated['nodes'])->where('type', 'action')->values()->all();

        $workflow = Workflow::create([
            'agency_id' => $agency->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'trigger_type' => $triggerNode['subtype'] ?? 'manual',
            'trigger_config' => $triggerNode['config'] ?? [],
            'actions' => array_map(fn($node) => [
                'type' => $node['subtype'],
                'config' => $node['config'] ?? [],
            ], $actionNodes),
            'conditions' => [],
            'status' => WorkflowStatus::DRAFT->value,
        ]);

        // Create initial version
        $workflow->createVersion('Created from visual builder', Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Workflow saved successfully.',
            'workflow' => $workflow,
        ]);
    }

    /**
     * Create workflow from template.
     */
    public function createFromTemplate(Request $request, string $templateSlug)
    {
        $agency = $request->user()->agency;
        $template = WorkflowTemplate::where('slug', $templateSlug)->firstOrFail();

        $workflow = Workflow::create([
            'agency_id' => $agency->id,
            'name' => $template->name,
            'slug' => Str::slug($template->name) . '-' . uniqid(),
            'trigger_type' => $template->nodes[0]['subtype'] ?? 'manual',
            'trigger_config' => [],
            'actions' => collect($template->nodes)->where('type', 'action')->values()->map(fn($node) => [
                'type' => $node['subtype'],
                'config' => $node['config'] ?? [],
            ])->all(),
            'conditions' => [],
            'status' => WorkflowStatus::DRAFT->value,
        ]);

        // Create initial version and increment template usage
        $workflow->createVersion('Created from template: ' . $template->name, Auth::id());
        $template->incrementUsage();

        return redirect()->route('workflows.builder')
            ->with('success', 'Workflow created from template: ' . $template->name);
    }

    /**
     * Show workflow versions.
     */
    public function versions(Request $request, Workflow $workflow)
    {
        $agency = $request->user()->agency;

        if ($workflow->agency_id !== $agency->id) {
            abort(403);
        }

        $versions = $workflow->versions()->orderBy('version_number', 'desc')->paginate(15);

        return view('workflows.versions', compact('agency', 'workflow', 'versions'));
    }

    /**
     * Restore workflow to a specific version.
     */
    public function restoreVersion(Request $request, Workflow $workflow, int $versionId)
    {
        $agency = $request->user()->agency;

        if ($workflow->agency_id !== $agency->id) {
            abort(403);
        }

        $version = $workflow->versions()->findOrFail($versionId);

        // Create a version of current state before restoring
        $workflow->createVersion('Before restore to v' . $version->version_number, Auth::id());

        // Restore
        $workflow->restoreFromVersion($version);

        return redirect()->route('workflows.show', $workflow)
            ->with('success', 'Workflow restored to version ' . $version->version_number);
    }

    /**
     * Show webhook info for a workflow.
     */
    public function webhookInfo(Request $request, Workflow $workflow)
    {
        $agency = $request->user()->agency;

        if ($workflow->agency_id !== $agency->id) {
            abort(403);
        }

        if (!$workflow->webhook_secret) {
            $workflow->generateWebhookSecret();
        }

        $webhookLogs = $workflow->webhookLogs()->orderBy('created_at', 'desc')->paginate(20);

        return view('workflows.webhook', compact('agency', 'workflow', 'webhookLogs'));
    }

    /**
     * Regenerate webhook secret.
     */
    public function regenerateWebhook(Request $request, Workflow $workflow)
    {
        $agency = $request->user()->agency;

        if ($workflow->agency_id !== $agency->id) {
            abort(403);
        }

        $workflow->generateWebhookSecret();

        return back()->with('success', 'Webhook secret regenerated. Please update your external service.');
    }
}
