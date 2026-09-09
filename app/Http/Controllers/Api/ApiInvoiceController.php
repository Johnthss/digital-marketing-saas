<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request): JsonResponse
    {
        $agency = $request->user()->agency;
        $query = Invoice::where('agency_id', $agency->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return InvoiceResource::collection($invoices)->response();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'total' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $agency = $request->user()->agency;
        $invoice = Invoice::create([
            'agency_id' => $agency->id,
            'invoice_number' => Invoice::generateNumber(),
            'status' => 'pending',
            'issue_date' => now(),
            'due_date' => $data['due_date'] ?? now()->addDays(30),
            ...$data,
        ]);

        return (new InvoiceResource($invoice))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorizeAccess($request, $invoice);

        return (new InvoiceResource($invoice->load('items', 'client')))->response();
    }

    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorizeAccess($request, $invoice);

        $data = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'total' => 'sometimes|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($data);

        return (new InvoiceResource($invoice))->response();
    }

    public function destroy(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorizeAccess($request, $invoice);
        $invoice->delete();

        return response()->json(null, 204);
    }

    private function authorizeAccess(Request $request, Invoice $invoice): void
    {
        $agency = $request->user()->agency;
        if ($invoice->agency_id !== $agency->id) {
            abort(404);
        }
    }
}
