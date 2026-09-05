<?php

namespace Tests\Unit\Services;

use App\Models\Agency;
use App\Models\Plan;
use App\Services\QuotaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotaServiceTest extends TestCase
{
    use RefreshDatabase;

    private QuotaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(QuotaService::class);
    }

    public function test_remaining_posts_returns_correct_value(): void
    {
        $agency = Agency::factory()->create(['subscription_plan' => 'starter', 'posts_count' => 10]);
        $this->assertEquals(90, $this->service->remainingPosts($agency));
    }

    public function test_remaining_posts_returns_unlimited_for_enterprise(): void
    {
        $agency = Agency::factory()->create(['subscription_plan' => 'enterprise', 'posts_count' => 9999]);
        $this->assertEquals(-1, $this->service->remainingPosts($agency));
    }

    public function test_is_over_quota_returns_true_when_exceeded(): void
    {
        $agency = Agency::factory()->create(['subscription_plan' => 'starter', 'posts_count' => 100]);
        $this->assertTrue($this->service->isOverQuota($agency, 'posts'));
    }

    public function test_is_over_quota_returns_false_when_within(): void
    {
        $agency = Agency::factory()->create(['subscription_plan' => 'starter', 'posts_count' => 50]);
        $this->assertFalse($this->service->isOverQuota($agency, 'posts'));
    }

    public function test_usage_percentage_returns_correct_value(): void
    {
        $agency = Agency::factory()->create(['subscription_plan' => 'starter', 'posts_count' => 50]);
        $this->assertEquals(50.0, $this->service->usagePercentage($agency, 'posts'));
    }

    public function test_increment_post_count_works(): void
    {
        $agency = Agency::factory()->create(['posts_count' => 0]);
        $this->service->incrementPostCount($agency);
        $this->assertEquals(1, $agency->fresh()->posts_count);
    }

    public function test_decrement_post_count_works(): void
    {
        $agency = Agency::factory()->create(['posts_count' => 5]);
        $this->service->decrementPostCount($agency);
        $this->assertEquals(4, $agency->fresh()->posts_count);
    }

    public function test_get_quota_status_returns_all_quotas(): void
    {
        $agency = Agency::factory()->create(['subscription_plan' => 'starter']);
        $status = $this->service->getQuotaStatus($agency);
        $this->assertArrayHasKey('posts', $status);
        $this->assertArrayHasKey('ai_generations', $status);
        $this->assertArrayHasKey('social_accounts', $status);
    }

    public function test_can_publish_post_returns_true_within_quota(): void
    {
        $agency = Agency::factory()->create(['subscription_plan' => 'starter', 'posts_count' => 0]);
        $this->assertTrue($this->service->canPublishPost($agency));
    }

    public function test_can_publish_post_returns_false_over_quota(): void
    {
        $agency = Agency::factory()->create(['subscription_plan' => 'starter', 'posts_count' => 100]);
        $this->assertFalse($this->service->canPublishPost($agency));
    }
}
