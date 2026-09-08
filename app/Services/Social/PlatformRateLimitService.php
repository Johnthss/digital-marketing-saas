<?php

namespace App\Services\Social;

use App\Models\SocialAccount;
use Illuminate\Support\Facades\Cache;

class PlatformRateLimitService
{
    /**
     * Check if the account is allowed to make a request.
     */
    public function isAllowed(SocialAccount $account, string $platform): bool
    {
        $limits = config("platform.social.rate_limits.{$platform}");

        if (! $limits) {
            return true;
        }

        $hourlyKey = "rate_limit:{$platform}:hourly:{$account->id}";
        $dailyKey = "rate_limit:{$platform}:daily:{$account->id}";

        $hourlyCount = Cache::get($hourlyKey, 0);
        $dailyCount = Cache::get($dailyKey, 0);

        return $hourlyCount < $limits['requests_per_hour']
            && $dailyCount < $limits['requests_per_day'];
    }

    /**
     * Record a request being made.
     */
    public function recordRequest(SocialAccount $account, string $platform): void
    {
        $hourlyKey = "rate_limit:{$platform}:hourly:{$account->id}";
        $dailyKey = "rate_limit:{$platform}:daily:{$account->id}";

        $hourlyTtl = now()->addHour();
        $dailyTtl = now()->addDay();

        Cache::put($hourlyKey, Cache::get($hourlyKey, 0) + 1, $hourlyTtl);
        Cache::put($dailyKey, Cache::get($dailyKey, 0) + 1, $dailyTtl);
    }

    /**
     * Get current rate limit status.
     */
    public function getStatus(SocialAccount $account, string $platform): array
    {
        $limits = config("platform.social.rate_limits.{$platform}", []);
        $hourlyKey = "rate_limit:{$platform}:hourly:{$account->id}";
        $dailyKey = "rate_limit:{$platform}:daily:{$account->id}";

        $hourlyCount = Cache::get($hourlyKey, 0);
        $dailyCount = Cache::get($dailyKey, 0);

        return [
            'hourly' => [
                'used' => $hourlyCount,
                'limit' => $limits['requests_per_hour'] ?? PHP_INT_MAX,
                'remaining' => max(0, ($limits['requests_per_hour'] ?? PHP_INT_MAX) - $hourlyCount),
            ],
            'daily' => [
                'used' => $dailyCount,
                'limit' => $limits['requests_per_day'] ?? PHP_INT_MAX,
                'remaining' => max(0, ($limits['requests_per_day'] ?? PHP_INT_MAX) - $dailyCount),
            ],
        ];
    }

    /**
     * Calculate the number of seconds to wait before retrying.
     * Used with queue job backoff for non-blocking rate limit handling.
     */
    public function getRetryAfterSeconds(SocialAccount $account, string $platform): int
    {
        if ($this->isAllowed($account, $platform)) {
            return 0;
        }

        $limits = config("platform.social.rate_limits.{$platform}");
        $hourlyKey = "rate_limit:{$platform}:hourly:{$account->id}";
        $hourlyTtl = Cache::get($hourlyKey) ? now()->addHour()->diffInSeconds(now()) : 60;

        return min($hourlyTtl, 3600);
    }
}
