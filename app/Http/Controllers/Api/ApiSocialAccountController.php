<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiSocialAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'agency']);
    }

    public function index(Request $request): JsonResponse
    {
        $agency = $request->user()->agency;
        $accounts = SocialAccount::where('agency_id', $agency->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($accounts);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'platform' => 'required|string|in:facebook,instagram,twitter,linkedin,tiktok,pinterest',
            'account_name' => 'required|string|max:255',
            'account_handle' => 'nullable|string|max:255',
            'access_token' => 'required|string',
        ]);

        $agency = $request->user()->agency;
        $account = SocialAccount::create([
            'agency_id' => $agency->id,
            ...$request->validated(),
        ]);

        return response()->json($account, 201);
    }

    public function show(Request $request, SocialAccount $account): JsonResponse
    {
        $this->authorizeAccess($request, $account);

        return response()->json($account);
    }

    public function update(Request $request, SocialAccount $account): JsonResponse
    {
        $this->authorizeAccess($request, $account);

        $request->validate([
            'account_name' => 'sometimes|string|max:255',
            'account_handle' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $account->update($request->validated());

        return response()->json($account);
    }

    public function destroy(Request $request, SocialAccount $account): JsonResponse
    {
        $this->authorizeAccess($request, $account);
        $account->delete();

        return response()->json(null, 204);
    }

    private function authorizeAccess(Request $request, SocialAccount $account): void
    {
        $agency = $request->user()->agency;
        if ($account->agency_id !== $agency->id) {
            abort(404);
        }
    }
}
