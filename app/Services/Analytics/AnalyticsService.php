<?php

namespace App\Services\Analytics;

use App\Models\Agency;
use App\Models\SocialPost;
use App\Models\EmailCampaign;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\AiContentLog;
use App\Models\SocialAccount;
use App\Models\SocialListeningMention;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Return all core dashboard stats for an agency.
     */
    public function getDashboardStats(Agency $agency): array
    {
        return [
            'overview' => $this->getOverviewStats($agency),
            'social' => $this->getSocialStats($agency),
            'email' => $this->getEmailStats($agency),
            'financial' => $this->getFinancialStats($agency),
            'ai' => $this->getAiStats($agency),
        ];
    }

    /**
     * High-level overview: clients, posts, campaigns, revenue.
     */
    public function getOverviewStats(Agency $agency): array
    {
        return [
            'total_clients' => Client::where('agency_id', $agency->id)
                ->where('status', 'active')->count(),
            'total_posts' => SocialPost::where('agency_id', $agency->id)->count(),
            'total_campaigns' => Campaign::where('agency_id', $agency->id)->count(),
            'total_revenue' => Invoice::where('agency_id', $agency->id)
                ->where('status', 'paid')->sum('total'),
            'pending_invoices' => Invoice::where('agency_id', $agency->id)
                ->where('status', 'pending')->count(),
            'active_social_accounts' => SocialAccount::where('agency_id', $agency->id)
                ->where('is_connected', true)->count(),
        ];
    }

    /**
     * Social media stats.
     */
    public function getSocialStats(Agency $agency): array
    {
        $posts = SocialPost::where('agency_id', $agency->id)->get();

        return [
            'total_posts' => $posts->count(),
            'published' => $posts->where('status', 'published')->count(),
            'scheduled' => $posts->where('status', 'scheduled')->count(),
            'failed' => $posts->where('status', 'failed')->count(),
            'draft' => $posts->where('status', 'draft')->count(),
            'average_engagement' => $posts->where('status', 'published')->avg('engagement_rate') ?? 0,
            'total_engagement' => $posts->sum('engagement_rate') ?? 0,
            'by_platform' => $this->groupByPlatform($agency, 'social_posts'),
        ];
    }

    /**
     * Email marketing stats.
     */
    public function getEmailStats(Agency $agency): array
    {
        $campaigns = EmailCampaign::where('agency_id', $agency->id)->get();

        return [
            'total_campaigns' => $campaigns->count(),
            'sent_campaigns' => $campaigns->where('status', 'sent')->count(),
            'draft_campaigns' => $campaigns->where('status', 'draft')->count(),
            'scheduled_campaigns' => $campaigns->where('status', 'scheduled')->count(),
            'total_sent' => $campaigns->sum('sent_count') ?? 0,
            'total_opened' => $campaigns->sum('opened_count') ?? 0,
            'total_clicked' => $campaigns->sum('clicked_count') ?? 0,
            'average_open_rate' => $campaigns->where('status', 'sent')->avg('open_rate') ?? 0,
            'average_click_rate' => $campaigns->where('status', 'sent')->avg('click_rate') ?? 0,
        ];
    }

    /**
     * Financial stats.
     */
    public function getFinancialStats(Agency $agency): array
    {
        $invoices = Invoice::where('agency_id', $agency->id)->get();

        return [
            'total_revenue' => $invoices->where('status', 'paid')->sum('total') ?? 0,
            'pending_amounts' => $invoices->where('status', 'pending')->sum('total') ?? 0,
            'overdue_amounts' => $invoices->where('status', 'overdue')->sum('total') ?? 0,
            'total_invoices' => $invoices->count(),
            'paid_invoices' => $invoices->where('status', 'paid')->count(),
            'pending_invoices' => $invoices->where('status', 'pending')->count(),
            'overdue_invoices' => $invoices->where('status', 'overdue')->count(),
        ];
    }

    /**
     * AI usage stats.
     */
    public function getAiStats(Agency $agency): array
    {
        $logs = AiContentLog::where('agency_id', $agency->id)->get();

        return [
            'total_generations' => $logs->count(),
            'successful_generations' => $logs->where('status', 'success')->count(),
            'failed_generations' => $logs->where('status', 'failed')->count(),
            'total_tokens_used' => $logs->sum('total_tokens') ?? 0,
            'total_cost_usd' => $logs->sum('cost_usd') ?? 0,
            'by_action' => $this->groupByType($logs, 'action'),
            'last_7_days' => $logs->where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * Clear cached analytics data for an agency.
     */
    public function clearCache(Agency $agency): void
    {
        // Analytics data is computed on-the-fly; no persistent cache to clear
        // Future: could implement Redis caching here
    }

    /**
     * Get total posts count scoped to agency.
     */
    public function getTotalPosts(Agency $agency): int
    {
        return SocialPost::where('agency_id', $agency->id)->count();
    }

    /**
     * Get total campaigns count scoped to agency.
     */
    public function getTotalCampaigns(Agency $agency): int
    {
        return EmailCampaign::where('agency_id', $agency->id)->count();
    }

    /**
     * Get total invoices count scoped to agency.
     */
    public function getTotalInvoices(Agency $agency): int
    {
        return Invoice::where('agency_id', $agency->id)->count();
    }

    /**
     * Get posts grouped by platform.
     */
    private function groupByPlatform(Agency $agency, string $table): array
    {
        $posts = SocialPost::where('agency_id', $agency->id)
            ->selectRaw('platform, count(*) as count')
            ->groupBy('platform')
            ->get();

        $result = [];
        foreach ($posts as $post) {
            $result[$post->platform] = $post->count;
        }

        return $result;
    }

    /**
     * Get logs grouped by type.
     */
    private function groupByType(Collection $logs, string $field): array
    {
        $result = [];
        foreach ($logs as $log) {
            $key = $log->$field ?? 'unknown';
            $result[$key] = ($result[$key] ?? 0) + 1;
        }

        return $result;
    }

    /**
     * Get posts by status.
     */
    public function getPostsByStatus(Agency $agency): array
    {
        $posts = SocialPost::where('agency_id', $agency->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        $result = [];
        foreach ($posts as $post) {
            $result[$post->status] = $post->count;
        }

        return $result;
    }

    /**
     * Get email campaigns by status.
     */
    public function getEmailCampaignsByStatus(Agency $agency): array
    {
        $campaigns = EmailCampaign::where('agency_id', $agency->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        $result = [];
        foreach ($campaigns as $campaign) {
            $result[$campaign->status] = $campaign->count;
        }

        return $result;
    }

    /**
     * Get total revenue for a date range.
     */
    public function getRevenueForRange(Agency $agency, string $startDate, string $endDate): float
    {
        return Invoice::where('agency_id', $agency->id)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('total') ?? 0;
    }

    /**
     * Get AI cost for a date range.
     */
    public function getAiCostForRange(Agency $agency, string $startDate, string $endDate): float
    {
        return AiContentLog::where('agency_id', $agency->id)
            ->where('status', 'success')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('cost_usd') ?? 0;
    }

    /**
     * Get top performing posts (by engagement rate).
     */
    public function getTopPerformingPosts(Agency $agency, int $limit = 10): Collection
    {
        return SocialPost::where('agency_id', $agency->id)
            ->where('status', 'published')
            ->orderByDesc('engagement_rate')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top email campaigns (by open rate).
     */
    public function getTopEmailCampaigns(Agency $agency, int $limit = 10): Collection
    {
        return EmailCampaign::where('agency_id', $agency->id)
            ->where('status', 'sent')
            ->orderByDesc('open_rate')
            ->limit($limit)
            ->get();
    }
}
