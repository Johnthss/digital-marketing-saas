<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\SocialPost;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\AiContentLog;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $agency = $user->agency;
        $range = $request->get('range', '30');
        $startDate = Carbon::now()->subDays($range);

        $postStats = [
            'total_posts' => SocialPost::where('agency_id', $agency->id)->count(),
            'published_posts' => SocialPost::where('agency_id', $agency->id)->published()->count(),
            'scheduled_posts' => SocialPost::where('agency_id', $agency->id)->scheduled()->count(),
            'failed_posts' => SocialPost::where('agency_id', $agency->id)->where('status', 'failed')->count(),
            'avg_quality_score' => SocialPost::where('agency_id', $agency->id)->avg('quality_score') ?? 0,
        ];

        $engagement = SocialPost::where('agency_id', $agency->id)
            ->published()
            ->select(
                DB::raw('SUM(views_count) as total_views'),
                DB::raw('SUM(likes_count) as total_likes'),
                DB::raw('SUM(comments_count) as total_comments'),
                DB::raw('SUM(shares_count) as total_shares'),
                DB::raw('SUM(clicks_count) as total_clicks')
            )
            ->first();

        $platformStats = SocialPost::where('agency_id', $agency->id)
            ->select('platform', DB::raw('count(*) as total'))
            ->groupBy('platform')
            ->get()
            ->keyBy('platform');

        $campaignStats = [
            'total' => Campaign::where('agency_id', $agency->id)->count(),
            'active' => Campaign::where('agency_id', $agency->id)->active()->count(),
            'completed' => Campaign::where('agency_id', $agency->id)->where('status', 'completed')->count(),
        ];

        $clientStats = [
            'total' => Client::where('agency_id', $agency->id)->count(),
            'active' => Client::where('agency_id', $agency->id)->where('status', 'active')->count(),
            'leads' => Client::where('agency_id', $agency->id)->where('status', 'lead')->count(),
        ];

        $aiStats = [
            'total_generations' => AiContentLog::where('agency_id', $agency->id)->count(),
            'successful' => AiContentLog::where('agency_id', $agency->id)->where('status', 'success')->count(),
            'total_cost' => AiContentLog::where('agency_id', $agency->id)->sum('cost_usd'),
            'total_tokens' => AiContentLog::where('agency_id', $agency->id)->sum('total_tokens'),
        ];

        $revenueStats = [
            'total' => Invoice::where('agency_id', $agency->id)->sum('total'),
            'paid' => Invoice::where('agency_id', $agency->id)->paid()->sum('total'),
            'pending' => Invoice::where('agency_id', $agency->id)->pending()->sum('total'),
        ];

        $dailyEngagement = SocialPost::where('agency_id', $agency->id)
            ->published()
            ->where('published_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(published_at) as date'),
                DB::raw('SUM(likes_count + comments_count + shares_count) as engagement')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $bestPosts = SocialPost::where('agency_id', $agency->id)
            ->published()
            ->orderBy('likes_count', 'desc')
            ->limit(5)
            ->get();

        return view('analytics.index', compact(
            'agency', 'postStats', 'engagement', 'platformStats',
            'campaignStats', 'clientStats', 'aiStats', 'revenueStats',
            'dailyEngagement', 'bestPosts', 'range'
        ));
    }
}
