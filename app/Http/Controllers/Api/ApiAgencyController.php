<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgencyResource;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\UserResource;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAgencyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function settings(Request $request): JsonResponse
    {
        $agency = $request->user()->agency;

        return (new AgencyResource($agency))->response();
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'website' => 'nullable|url',
            'timezone' => 'nullable|string|max:50',
            'currency' => 'nullable|string|size:3',
        ]);

        $agency = $request->user()->agency;
        $agency->update($request->validated());

        return (new AgencyResource($agency))->response();
    }

    public function team(Request $request): JsonResponse
    {
        $agency = $request->user()->agency;
        $users = $agency->users()->orderBy('created_at', 'desc')->get();

        return UserResource::collection($users)->response();
    }

    public function billing(Request $request): JsonResponse
    {
        $agency = $request->user()->agency;
        $invoices = Invoice::where('agency_id', $agency->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return InvoiceResource::collection($invoices)->response();
    }
}
