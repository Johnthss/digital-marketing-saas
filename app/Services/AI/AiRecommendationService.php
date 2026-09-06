<?php

namespace App\Services\AI;

use App\Models\Agency;
use App\Models\SocialPost;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AiRecommendationService
{
    /**
     * Get content recommendations for an agency.
     */
    public function getContentRecommendations(Agency $agency, string $platform, int $count = 5): array
    {
        $posts = SocialPost::where('agency_id', $agency->id)
            ->where('platform', $platform)
            ->where('status', 'published')
            ->orderByDesc('engagement_rate')
            ->limit($count * 3)
            ->get();

        $recommendations = [];
        foreach ($posts->take($count) as $post) {
            $recommendations[] = [
                'title' => $post->content ? substr($post->content, 0, 100) . '...' : 'Untitled',
                'type' => 'content_recommendation',
                'content' => $post->content ?? '',
                'hashtags' => $this->extractHashtags($post->content ?? ''),
                'best_time' => $post->published_at?->format('H:i') ?? 'N/A',
                'engagement_rate' => $post->engagement_rate ?? 0,
                'scores' => [
                    'engagement' => $post->engagement_rate ?? 0,
                    'reach' => $post->views_count ?? 0,
                    'impressions' => ($post->views_count ?? 0) + ($post->likes_count ?? 0),
                ],
            ];
        }

        return $recommendations;
    }

    /**
     * Get optimal posting times for a platform.
     */
    public function getOptimalPostingTimes(Agency $agency, string $platform): array
    {
        $bestHours = $this->getBestHoursForPlatform($platform);
        $bestDays = $this->getBestDaysForPlatform($platform);

        return [
            'platform' => $platform,
            'best_hours' => $bestHours,
            'best_days' => $bestDays,
            'timezone' => $agency->timezone ?? 'UTC',
        ];
    }

    /**
     * Generate hashtags for a topic.
     */
    public function generateHashtags(Agency $agency, string $topic, string $platform, int $count = 10): array
    {
        $baseHashtags = [
            '#marketing', '#digitalmarketing', '#contentmarketing',
            '#socialmedia', '#branding', '#business', '#entrepreneur',
            '#smallbusiness', '#marketingtips', '#contentcreator',
            '#instagram', '#facebook', '#twitter', '#linkedin',
            '#tiktok', '#pinterest', '#youtube', '#threads',
        ];

        $platformSpecific = [
            'instagram' => ['#instagramreels', '#instagood', '#photooftheday', '#reels'],
            'facebook' => ['#facebookmarketing', '#fb', '#facebookpost'],
            'twitter' => ['#twitter', '#tweet', '#viral', '#trending'],
            'linkedin' => ['#linkedin', '#business', '#professional', '#networking', '#career'],
            'tiktok' => ['#tiktok', '#fyp', '#viral', '#trending', '#dance'],
            'pinterest' => ['#pinterest', '#pinterestideas', '#inspiration', '#diy'],
            'threads' => ['#threads', '#meta', '#conversation', '#discussion'],
        ];

        $hashtags = array_merge($baseHashtags, $platformSpecific[$platform] ?? []);
        $hashtags = array_slice($hashtags, 0, $count);

        return array_values(array_unique($hashtags));
    }

    /**
     * Analyze content quality.
     */
    public function analyzeContentQuality(string $content, string $platform): array
    {
        $length = strlen($content);
        $wordCount = str_word_count($content);
        $hashtagCount = substr_count($content, '#');
        $mentionCount = substr_count($content, '@');
        $hasEmoji = preg_match('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}]/u', $content) ? 1 : 0;
        $hasQuestion = substr_count($content, '?');
        $hasCallToAction = stripos($content, 'click') !== false || stripos($content, 'link') !== false || stripos($content, 'learn more') !== false;

        // Score calculation (0-100)
        $score = 0;
        $score += min($length / 10, 20); // Length contribution
        $score += min($wordCount / 2, 20); // Word count contribution
        $score += $hashtagCount * 5; // Hashtags
        $score += $mentionCount * 3; // Mentions
        $score += $hasEmoji * 10; // Emoji presence
        $score += $hasQuestion * 10; // Question
        $score += $hasCallToAction * 15; // CTA
        $score = min($score, 100);

        // Engagement prediction (0-100)
        $engagementPrediction = $score * 0.7 + ($hasQuestion ? 15 : 0) + ($hasCallToAction ? 15 : 0);

        return [
            'score' => round($score),
            'engagement_prediction' => round($engagementPrediction),
            'metrics' => [
                'length' => $length,
                'word_count' => $wordCount,
                'hashtags' => $hashtagCount,
                'mentions' => $mentionCount,
                'has_emoji' => (bool) $hasEmoji,
                'has_question' => $hasQuestion > 0,
                'has_cta' => $hasCallToAction,
            ],
            'suggestions' => $this->generateSuggestions($content, $platform, $score, $hashtagCount, $wordCount),
            'platform_best_practices' => $this->getPlatformBestPractices($platform),
        ];
    }

    /**
     * Get post performance analytics.
     */
    public function getPostPerformanceAnalytics(Agency $agency, string $platform = null): array
    {
        $query = SocialPost::where('agency_id', $agency->id);

        if ($platform) {
            $query->where('platform', $platform);
        }

        $posts = $query->where('status', 'published')->get();

        if ($posts->isEmpty()) {
            return [
                'total_posts' => 0,
                'average_engagement' => 0,
                'top_performing' => [],
                'worst_performing' => [],
                'trends' => [],
            ];
        }

        $avgEngagement = $posts->avg('engagement_rate') ?? 0;
        $topPosts = $posts->sortByDesc('engagement_rate')->take(5)->values();
        $worstPosts = $posts->sortBy('engagement_rate')->take(5)->values();

        // Hourly trends
        $hourlyTrends = $posts->groupBy(function ($post) {
            return $post->published_at?->hour ?? 0;
        })->map(function ($group) {
            return [
                'hour' => $group->first()->published_at?->hour ?? 0,
                'avg_engagement' => $group->avg('engagement_rate') ?? 0,
                'count' => $group->count(),
            ];
        })->sortBy('hour')->values();

        return [
            'total_posts' => $posts->count(),
            'average_engagement' => round($avgEngagement, 2),
            'median_engagement' => round($posts->median('engagement_rate') ?? 0, 2),
            'max_engagement' => round($posts->max('engagement_rate') ?? 0, 2),
            'min_engagement' => round($posts->min('engagement_rate') ?? 0, 2),
            'top_performing
