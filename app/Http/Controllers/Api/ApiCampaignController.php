<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiCampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request): JsonResponse
    {
        $agency = $request->user()->agency;
        $query = Campaign::where('agency_id', $agency->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $campaigns = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return CampaignResource::collection($campaigns)->response();
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|in:general,social,email,mixed',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $agency = $request->user()->agency;
        $campaign = Campaign::create([
            'agency_id' => $agency->id,
            ...$request->validated(),
        ]);

        return (new CampaignResource($campaign))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Campaign $campaign): JsonResponse
    {
        $this->authorizeAccess($request, $campaign);

        return (new CampaignResource($campaign->load('posts', 'clients')))->response();
    }

    public function update(Request $request, Campaign $campaign): JsonResponse
    {
        $this->authorizeAccess($request, $campaign);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'nullable|string|in:general,social,email,mixed',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $campaign->update($request->validated());

        return (new CampaignResource($campaign))->response();
    }

    public function destroy(Request $request, Campaign $campaign): JsonResponse
    {
        $this->authorizeAccess($request, $campaign);
        $campaign->delete();

        return response()->json(null, 204);
    }

    private function authorizeAccess(Request $request, Campaign $campaign): void
    {
        $agency = $request->user()->agency;
        if ($campaign->agency_id !== $agency->id) {
            abort(404);
        }
    }
}
