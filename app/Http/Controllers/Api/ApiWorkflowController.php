<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workflow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiWorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request): JsonResponse
    {
        $agency = $request->user()->agency;
        $workflows = Workflow::where('agency_id', $agency->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($workflows);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'trigger_type' => 'required|string',
            'actions' => 'required|array',
        ]);

        $agency = $request->user()->agency;
        $workflow = Workflow::create([
            'agency_id' => $agency->id,
            ...$request->validated(),
        ]);

        return response()->json($workflow, 201);
    }

    public function show(Request $request, Workflow $workflow): JsonResponse
    {
        $this->authorizeAccess($request, $workflow);

        return response()->json($workflow->load('executions', 'versions'));
    }

    public function update(Request $request, Workflow $workflow): JsonResponse
    {
        $this->authorizeAccess($request, $workflow);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'trigger_type' => 'sometimes|string',
            'actions' => 'sometimes|array',
            'status' => 'sometimes|in:active,paused',
        ]);

        $workflow->update($request->validated());

        return response()->json($workflow);
    }

    public function destroy(Request $request, Workflow $workflow): JsonResponse
    {
        $this->authorizeAccess($request, $workflow);
        $workflow->delete();

        return response()->json(null, 204);
    }

    private function authorizeAccess(Request $request, Workflow $workflow): void
    {
        $agency = $request->user()->agency;
        if ($workflow->agency_id !== $agency->id) {
            abort(404);
        }
    }
}
