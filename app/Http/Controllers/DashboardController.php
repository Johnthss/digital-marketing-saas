<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\SocialPost;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\AiContentLog;
use App\Models\ActivityLog;
use App\Models\SocialAccount;
use App\Services\QuotaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request, QuotaService $quota)
    {
        $agency = $request->user()->agency;

        // Post stats
        $totalPosts = SocialPost::where('agency_id', $agency->id)->count();
        $publishedPosts = SocialPost::where('agency_id', $agency->id)->where('status', 'published')->count();
        $scheduledPosts = SocialPost::where('agency_id', $agency->id)->where('status', 'scheduled')->count();
        $failedPosts = SocialPost::where('agency_id', $agency->id)->where('status', 'failed')->count();

        $stats = [
            'totalPosts' => $totalPosts,
            'publishedPosts' => $publishedPosts,
            'scheduledPosts' => $scheduledPosts,
            'failedPosts' => $failedPosts,
            'totalCampaigns' => Campaign::where('agency_id', $agency->id)->count(),
            'totalClients' => Client::where('agency_id', $agency->id)->count(),
            'totalInvoices' => Invoice::where('agency_id', $agency->id)->count(),
            'aiGenerations' => AiContentLog::where('agency_id', $agency->id)->count(),
        ];

        // Quotas
        $quotas = [
            'posts' => [
                'label' => 'Social Posts',
                'used' => $totalPosts,
                'limit' => $quota->getLimit($agency, 'posts'),
                'percentage' => $quota->getPercentage($agency, 'posts', $totalPosts),
            ],
            'ai' => [
                'label' => 'AI Generations',
                'used' => $stats['aiGenerations'],
                'limit' => $quota->getLimit($agency, 'ai_generations'),
                'percentage' => $quota->getPercentage($agency, 'ai_generations', $stats['aiGenerations']),
            ],
            'campaigns' => [
                'label' => 'Campaigns',
                'used' => $stats['totalCampaigns'],
                'limit' => $quota->getLimit($agency, 'campaigns'),
                'percentage' => $quota->getPercentage($agency, 'campaigns', $stats['totalCampaigns']),
            ],
            'clients' => [
                'label' => 'Clients',
                'used' => $stats['totalClients'],
                'limit' => $quota->getLimit($agency, 'clients'),
                'percentage' => $quota->getPercentage($agency, 'clients', $stats['totalClients']),
            ],
            'users' => [
                'label' => 'Team Members',
                'used' => $agency->users()->count(),
                'limit' => $quota->getLimit($agency, 'users'),
                'percentage' => $quota->getPercentage($agency, 'users', $agency->users()->count()),
            ],
            'accounts' => [
                'label' => 'Social Accounts',
                'used' => SocialAccount::where('agency_id', $agency->id)->count(),
                'limit' => $quota->getLimit($agency, 'social_accounts'),
                'percentage' => $quota->getPercentage($agency, 'social_accounts', SocialAccount::where('agency_id', $agency->id)->count()),
            ],
            'invoices' => [
                'label' => 'Invoices',
                'used' => $stats['totalInvoices'],
                'limit' => $quota->getLimit($agency, 'invoices'),
                'percentage' => $quota->getPercentage($agency, 'invoices', $stats['totalInvoices']),
            ],
            'landing_pages' => [
                'label' => 'Landing Pages',
                'used' => \App\Models\LandingPage::where('agency_id', $agency->id)->count(),
                'limit' => $quota->getLimit($agency, 'landing_pages'),
                'percentage' => $quota->getPercentage($agency, 'landing_pages', \App\Models\LandingPage::where('agency_id', $agency->id)->count()),
            ],
        ];

        // Recent activity
        $recentActivity = ActivityLog::where('agency_id', $agency->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Upcoming scheduled posts
        $upcomingPosts = SocialPost::where('agency_id', $agency->id)
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>', now())
            ->with('socialAccount')
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact('stats', 'quotas', 'recentActivity', 'upcomingPosts', 'agency'));
    }
}
