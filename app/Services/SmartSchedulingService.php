<?php

namespace App\Services;

use App\Models\SocialPost;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SmartSchedulingService
{
    /**
     * Platform-specific default optimal hours (24h format).
     * Used as fallback when insufficient historical data exists.
     */
    private const PLATFORM_DEFAULTS = [
        'facebook' => [9, 12, 15],
        'instagram' => [11, 14, 18],
        'twitter' => [8, 12, 17],
        'linkedin' => [8, 12, 17],
        'tiktok' => [12, 16, 20],
        'pinterest' => [14, 18, 21],
    ];

    /**
     * Minimum number of published posts required to use historical data.
     */
    private const MIN_HISTORICAL_POSTS = 5;

    /**
     * Get optimal posting times for a platform.
     *
     * @param  int  $limit  Number of suggestions to return
     * @param  int|null  $agencyId  Optional agency ID to scope historical data
     * @return Collection<int, array{day: string, hour: int, score: float, source: string}>
     */
    public function getOptimalTimes(string $platform, int $limit = 3, ?int $agencyId = null): Collection
    {
        $historical = $this->analyzeHistoricalData($platform, $agencyId);

        if ($historical->isEmpty()) {
            return $this->getDefaultTimes($platform, $limit);
        }

        return $historical->take($limit);
    }

    /**
     * Analyze historical engagement data to find optimal posting times.
     *
     * @return Collection<int, array{day: string, hour: int, score: float, source: string}>
     */
    public function analyzeHistoricalData(string $platform, ?int $agencyId = null): Collection
    {
        $query = SocialPost::query()
            ->where('platform', $platform)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where(function ($q) {
                $q->where('engagement_rate', '>', 0)
                    ->orWhere('likes_count', '>', 0)
                    ->orWhere('comments_count', '>', 0)
                    ->orWhere('shares_count', '>', 0);
            });

        if ($agencyId !== null) {
            $query->where('agency_id', $agencyId);
        }

        $posts = $query->get();

        if ($posts->count() < self::MIN_HISTORICAL_POSTS) {
            return collect();
        }

        // Group by day of week and hour, calculate average engagement
        $timeSlots = [];

        foreach ($posts as $post) {
            $publishedAt = Carbon::parse($post->published_at);
            $day = $publishedAt->format('l');
            $hour = (int) $publishedAt->format('G');

            $engagement = $this->calculateEngagementScore($post);
            $key = "{$day}-{$hour}";

            if (! isset($timeSlots[$key])) {
                $timeSlots[$key] = [
                    'day' => $day,
                    'hour' => $hour,
                    'total_engagement' => 0,
                    'count' => 0,
                ];
            }

            $timeSlots[$key]['total_engagement'] += $engagement;
            $timeSlots[$key]['count']++;
        }

        // Calculate average engagement per time slot
        $results = collect($timeSlots)->map(function ($slot) {
            $avgEngagement = $slot['total_engagement'] / $slot['count'];

            return [
                'day' => $slot['day'],
                'hour' => $slot['hour'],
                'score' => round($avgEngagement, 2),
                'source' => 'historical',
            ];
        });

        // Sort by score descending
        return $results->sortByDesc('score')->values();
    }

    /**
     * Get default optimal times for a platform when no historical data exists.
     *
     * @return Collection<int, array{day: string, hour: int, score: float, source: string}>
     */
    public function getDefaultTimes(string $platform, int $limit = 3): Collection
    {
        $hours = self::PLATFORM_DEFAULTS[$platform] ?? [9, 12, 17];
        $days = ['Tuesday', 'Wednesday', 'Thursday'];

        $results = collect();

        foreach ($days as $day) {
            foreach ($hours as $hour) {
                $results->push([
                    'day' => $day,
                    'hour' => $hour,
                    'score' => 0.0,
                    'source' => 'default',
                ]);
            }
        }

        return $results->take($limit);
    }

    /**
     * Calculate engagement score for a post.
     */
    public function calculateEngagementScore(SocialPost $post): float
    {
        $engagement = $post->likes_count
            + ($post->comments_count * 2)
            + ($post->shares_count * 3)
            + ($post->clicks_count * 1.5);

        $impressions = $post->metrics['impressions'] ?? 0;

        if ($impressions > 0) {
            return round(($engagement / $impressions) * 100, 2);
        }

        return round($engagement, 2);
    }

    /**
     * Get the next optimal posting datetime for a platform.
     */
    public function getNextOptimalTime(string $platform, ?int $agencyId = null): ?Carbon
    {
        $optimalTimes = $this->getOptimalTimes($platform, 1, $agencyId);

        if ($optimalTimes->isEmpty()) {
            return null;
        }

        $best = $optimalTimes->first();
        $dayMap = [
            'Sunday' => 0,
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
        ];

        $targetDay = $dayMap[$best['day']] ?? 2;
        $targetHour = $best['hour'];

        $now = Carbon::now();
        $candidate = $now->copy()->next($targetDay)->setTime($targetHour, 0, 0);

        if ($candidate->isPast()) {
            $candidate->addWeek();
        }

        return $candidate;
    }
}
