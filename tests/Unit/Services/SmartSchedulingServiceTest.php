<?php

namespace Tests\Unit\Services;

use App\Models\Agency;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Services\SmartSchedulingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartSchedulingServiceTest extends TestCase
{
    use RefreshDatabase;

    private SmartSchedulingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SmartSchedulingService::class);
    }

    public function test_get_default_times_returns_expected_structure(): void
    {
        $result = $this->service->getDefaultTimes('instagram', 3);

        $this->assertCount(3, $result);
        $this->assertEquals('default', $result->first()['source']);
        $this->assertArrayHasKey('day', $result->first());
        $this->assertArrayHasKey('hour', $result->first());
        $this->assertArrayHasKey('score', $result->first());
        $this->assertArrayHasKey('source', $result->first());
    }

    public function test_analyze_historical_data_returns_sorted_results(): void
    {
        $agency = Agency::factory()->create();
        $account = SocialAccount::factory()->create([
            'agency_id' => $agency->id,
            'platform' => 'instagram',
        ]);

        // Create 6 published posts with varying engagement at different times
        $times = [
            ['day' => 'Tuesday', 'hour' => 14, 'engagement' => 15.5],
            ['day' => 'Wednesday', 'hour' => 11, 'engagement' => 12.3],
            ['day' => 'Thursday', 'hour' => 18, 'engagement' => 8.7],
            ['day' => 'Monday', 'hour' => 9, 'engagement' => 5.2],
            ['day' => 'Friday', 'hour' => 12, 'engagement' => 3.1],
            ['day' => 'Tuesday', 'hour' => 16, 'engagement' => 1.8],
        ];

        foreach ($times as $time) {
            $publishedAt = new \DateTime("next {$time['day']}");
            $publishedAt->setTime($time['hour'], 0, 0);

            SocialPost::factory()->create([
                'agency_id' => $agency->id,
                'social_account_id' => $account->id,
                'platform' => 'instagram',
                'status' => 'published',
                'published_at' => $publishedAt->format('Y-m-d H:i:s'),
                'engagement_rate' => $time['engagement'],
                'likes_count' => (int) ($time['engagement'] * 10),
                'comments_count' => (int) ($time['engagement'] * 2),
                'shares_count' => (int) ($time['engagement'] * 1),
                'clicks_count' => (int) ($time['engagement'] * 3),
                'metrics' => ['impressions' => 100],
            ]);
        }

        $result = $this->service->analyzeHistoricalData('instagram', $agency->id);

        $this->assertNotEmpty($result);
        $this->assertGreaterThanOrEqual(5, $result->count());
        $this->assertEquals('historical', $result->first()['source']);

        // Results should be sorted by score descending
        $scores = $result->pluck('score')->toArray();
        $sortedScores = $scores;
        rsort($sortedScores);
        $this->assertEquals($sortedScores, $scores);
    }

    public function test_calculate_engagement_score_with_impressions(): void
    {
        $agency = Agency::factory()->create();
        $account = SocialAccount::factory()->create(['agency_id' => $agency->id]);

        $post = SocialPost::factory()->create([
            'agency_id' => $agency->id,
            'social_account_id' => $account->id,
            'likes_count' => 50,
            'comments_count' => 10,
            'shares_count' => 5,
            'clicks_count' => 20,
            'metrics' => ['impressions' => 1000],
        ]);

        $score = $this->service->calculateEngagementScore($post);

        // Expected: (50 + 10*2 + 5*3 + 20*1.5) / 1000 * 100 = (50 + 20 + 15 + 30) / 1000 * 100 = 115/1000*100 = 11.5
        $this->assertEquals(11.5, $score);
    }

    public function test_get_next_optimal_time_returns_future_datetime(): void
    {
        $result = $this->service->getNextOptimalTime('facebook');

        $this->assertNotNull($result);
        $this->assertTrue($result->isFuture());
    }
}
