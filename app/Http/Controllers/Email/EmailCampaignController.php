<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailCampaignRequest;
use App\Models\EmailCampaign;
use App\Services\Email\EmailCampaignService;
use Illuminate\Http\Request;

class EmailCampaignController extends Controller
{
    public function __construct(private EmailCampaignService $service)
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;
        $campaigns = EmailCampaign::where('agency_id', $agency->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('email.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('email.campaigns.create');
    }

    public function store(EmailCampaignRequest $request)
    {
        $agency = $request->user()->agency;
        $campaign = $this->service->create($agency, $request->validated());

        return redirect()->route('email.campaigns.show', $campaign)
            ->with('success', 'Campaign created successfully.');
    }

    public function show(EmailCampaign $campaign)
    {
        $this->authorize('view', $campaign);
        $campaign->load('recipients');
        return view('email.campaigns.show', compact('campaign'));
    }

    public function edit(EmailCampaign $campaign)
    {
        $this->authorize('update', $campaign);
        return view('email.campaigns.edit', compact('campaign'));
    }

    public function update(EmailCampaignRequest $request, EmailCampaign $campaign)
    {
        $this->authorize('update', $campaign);
        $campaign = $this->service->update($campaign, $request->validated());

        return redirect()->route('email.campaigns.show', $campaign)
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(EmailCampaign $campaign)
    {
        $this->authorize('delete', $campaign);
        $this->service->delete($campaign);

        return redirect()->route('email.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    public function send(EmailCampaign $campaign)
    {
        $this->authorize('update', $campaign);
        $this->service->send($campaign);

        return redirect()->route('email.campaigns.show', $campaign)
            ->with('success', 'Campaign is being sent!');
    }

    public function addClients(Request $request, EmailCampaign $campaign)
    {
        $this->authorize('update', $campaign);
        $agency = $request->user()->agency;
        $count = $this->service->addClientRecipients($campaign, $agency);

        return redirect()->route('email.campaigns.show', $campaign)
            ->with('success', "Added {$count} clients as recipients.");
    }
}
