<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiClientController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request): JsonResponse
    {
        $agency = $request->user()->agency;
        $query = Client::where('agency_id', $agency->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $clients = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return ClientResource::collection($clients)->response();
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $agency = $request->user()->agency;
        $client = Client::create([
            'agency_id' => $agency->id,
            ...$request->validated(),
        ]);

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Client $client): JsonResponse
    {
        $this->authorizeAccess($request, $client);

        return (new ClientResource($client->load('campaigns', 'invoices')))->response();
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $this->authorizeAccess($request, $client);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $client->update($request->validated());

        return (new ClientResource($client))->response();
    }

    public function destroy(Request $request, Client $client): JsonResponse
    {
        $this->authorizeAccess($request, $client);
        $client->delete();

        return response()->json(null, 204);
    }

    private function authorizeAccess(Request $request, Client $client): void
    {
        $agency = $request->user()->agency;
        if ($client->agency_id !== $agency->id) {
            abort(404);
        }
    }
}
