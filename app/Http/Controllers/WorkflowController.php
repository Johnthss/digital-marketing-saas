<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Workflow;
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
            'actions.*.type' => 'required|in:' . implode(',', array_keys(Workflow::ACTION_TYPES)),
            'actions.*.config' => 'nullable|array',
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

        return view('workflows.show', compact('agency', 'workflow', 'executions'));
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
        ]);

        $workflow->update([
            'name' => $validated['name'],
            'trigger_type' => $validated['trigger_type'],
            'trigger_config' => $validated['trigger_config'] ?? [],
            'actions' => $validated['actions'],
            'conditions' => $validated['conditions'] ?? [],
        ]);

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
}
