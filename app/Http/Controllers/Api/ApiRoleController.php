<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RBAC\EnterpriseRBACService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiRoleController extends Controller
{
    private EnterpriseRBACService $rbacService;

    public function __construct(EnterpriseRBACService $rbacService)
    {
        $this->rbacService = $rbacService;
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorizeRoleManagement($request);
        $agencyId = $request->user()->agency_id;
        $filters = $request->only(['action', 'user_id', 'date_from', 'date_to']);

        $auditTrail = $this->rbacService->getAuditTrail($agencyId, $filters);

        return response()->json([
            'data' => $auditTrail,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeRoleManagement($request);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $agencyId = $request->user()->agency_id;

        $role = $this->rbacService->createCustomRole(
            $agencyId,
            $validated['name'],
            $validated['permissions'] ?? []
        );

        return response()->json([
            'data' => $role->load('permissions'),
            'message' => 'Role created successfully.',
        ], 201);
    }

    public function show(Request $request, int $roleId): JsonResponse
    {
        $this->authorizeRoleManagement($request);
        $permissions = $this->rbacService->getRolePermissions($roleId, (int) $request->user()->agency_id);

        return response()->json([
            'data' => [
                'role_id' => $roleId,
                'permissions' => $permissions,
            ],
        ]);
    }

    public function update(Request $request, int $roleId): JsonResponse
    {
        $this->authorizeRoleManagement($request);
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $agencyId = $request->user()->agency_id;

        $success = $this->rbacService->updateRole($agencyId, $roleId, $validated);

        if (! $success) {
            return response()->json([
                'message' => 'Role not found or access denied.',
            ], 404);
        }

        return response()->json([
            'message' => 'Role updated successfully.',
        ]);
    }

    public function destroy(Request $request, int $roleId): JsonResponse
    {
        $this->authorizeRoleManagement($request);
        $agencyId = $request->user()->agency_id;

        $success = $this->rbacService->deleteRole($agencyId, $roleId);

        if (! $success) {
            return response()->json([
                'message' => 'Role not found or access denied.',
            ], 404);
        }

        return response()->json([
            'message' => 'Role deleted successfully.',
        ]);
    }

    public function assign(Request $request): JsonResponse
    {
        $this->authorizeRoleManagement($request);
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        $success = $this->rbacService->assignRoleToUser(
            $validated['user_id'],
            $validated['role_id'],
            (int) $request->user()->agency_id
        );

        if (! $success) {
            return response()->json([
                'message' => 'Failed to assign role.',
            ], 400);
        }

        return response()->json([
            'message' => 'Role assigned successfully.',
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        $this->authorizeRoleManagement($request);
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        $success = $this->rbacService->removeRoleFromUser(
            $validated['user_id'],
            $validated['role_id'],
            (int) $request->user()->agency_id
        );

        if (! $success) {
            return response()->json([
                'message' => 'Failed to remove role.',
            ], 400);
        }

        return response()->json([
            'message' => 'Role removed successfully.',
        ]);
    }

    public function userPermissions(Request $request, int $userId): JsonResponse
    {
        $this->authorizeRoleManagement($request);
        $permissions = $this->rbacService->getUserPermissions($userId, (int) $request->user()->agency_id);

        return response()->json([
            'data' => [
                'user_id' => $userId,
                'permissions' => $permissions,
            ],
        ]);
    }

    public function auditTrail(Request $request): JsonResponse
    {
        $this->authorizeRoleManagement($request);
        $agencyId = $request->user()->agency_id;
        $filters = $request->only(['action', 'user_id', 'date_from', 'date_to']);

        $auditTrail = $this->rbacService->getAuditTrail($agencyId, $filters);

        return response()->json([
            'data' => $auditTrail,
        ]);
    }

    private function authorizeRoleManagement(Request $request): void
    {
        abort_unless($request->user()->isOwner() || $request->user()->isAdmin(), 403);
    }
}
