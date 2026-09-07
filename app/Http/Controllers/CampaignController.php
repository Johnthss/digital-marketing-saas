<?php

namespace App\Http\Controllers;

use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;

        $query = Campaign::where('agency_id', $agency->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $campaigns = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('campaigns.index', compact('agency', 'campaigns'));
    }

    public function create(Request $request)
    {
        $agency = $request->user()->agency;
        $clients = Client::where('agency_id', $agency->id)->active()->get();
        $types = Campaign::CAMPAIGN_TYPES;

        return view('campaigns.create', compact('agency', 'clients', 'types'));
    }

    public function store(Request $request)
    {
        $agency = $request->user()->agency;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:'.implode(',', array_keys(Campaign::CAMPAIGN_TYPES)),
            'description' => 'nullable|string',
            'objective' => 'nullable|string|max:255',
            'target_audience' => 'nullable|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $campaign = Campaign::create([
            'agency_id' => $agency->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.uniqid(),
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'objective' => $validated['objective'] ?? null,
            'target_audience' => $validated['target_audience'] ?? null,
            'client_id' => $validated['client_id'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'status' => CampaignStatus::DRAFT->value,
        ]);

        $agency->increment('campaigns_count');

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign created successfully.');
    }

    public function show(Request $request, Campaign $campaign)
    {
        $agency = $request->user()->agency;

        if ($campaign->agency_id !== $agency->id) {
            abort(403);
        }

        $posts = $campaign->posts()->orderBy('created_at', 'desc')->paginate(10);

        return view('campaigns.show', compact('agency', 'campaign', 'posts'));
    }

    public function edit(Request $request, Campaign $campaign)
    {
        $agency = $request->user()->agency;

        if ($campaign->agency_id !== $agency->id) {
            abort(403);
        }

        $clients = Client::where('agency_id', $agency->id)->active()->get();
        $types = Campaign::CAMPAIGN_TYPES;

        return view('campaigns.edit', compact('agency', 'campaign', 'clients', 'types'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $agency = $request->user()->agency;

        if ($campaign->agency_id !== $agency->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:'.implode(',', array_keys(Campaign::CAMPAIGN_TYPES)),
            'description' => 'nullable|string',
            'objective' => 'nullable|string|max:255',
            'target_audience' => 'nullable|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $campaign->update($validated);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Request $request, Campaign $campaign)
    {
        $agency = $request->user()->agency;

        if ($campaign->agency_id !== $agency->id) {
            abort(403);
        }

        $campaign->delete();
        $agency->decrement('campaigns_count');

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign deleted.');
    }

    public function changeStatus(Request $request, Campaign $campaign)
    {
        $agency = $request->user()->agency;

        if ($campaign->agency_id !== $agency->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,active,paused,completed,cancelled',
        ]);

        $campaign->update(['status' => $validated['status']]);

        return back()->with('success', 'Campaign status updated.');
    }
}
