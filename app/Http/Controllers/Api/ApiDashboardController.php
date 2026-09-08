<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiContentLog;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\SocialPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request): JsonResponse
    {
        $agency = $request->user()->agency;

        return response()->json([
            'overview' => [
                'total_clients' => Client::where('agency_id', $agency->id)->count(),
                'total_posts' => SocialPost::where('agency_id', $agency->id)->count(),
                'total_campaigns' => Campaign::where('agency_id', $agency->id)->count(),
                'total_revenue' => Invoice::where('agency_id', $agency->id)->paid()->sum('total'),
                'pending_invoices' => Invoice::where('agency_id', $agency->id)->pending()->count(),
                'active_social_accounts' => $agency->socialAccounts()->count(),
            ],
            'social' => [
                'total_posts' => SocialPost::where('agency_id', $agency->id)->count(),
                'published' => SocialPost::where('agency_id', $agency->id)->published()->count(),
                'scheduled' => SocialPost::where('agency_id', $agency->id)->scheduled()->count(),
                'failed' => SocialPost::where('agency_id', $agency->id)->where('status', 'failed')->count(),
            ],
            'ai' => [
                'total_generations' => AiContentLog::where('agency_id', $agency->id)->count(),
                'successful' => AiContentLog::where('agency_id', $agency->id)->where('status', 'success')->count(),
                'total_cost' => AiContentLog::where('agency_id', $agency->id)->sum('cost_usd'),
            ],
        ]);
    }
}
