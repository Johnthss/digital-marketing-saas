<?php

namespace App\Services\Social;

use App\Enums\PostStatus;
use App\Models\SocialPost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SocialPostService
{
    public function __construct(private readonly SocialApiService $socialApi) {}

    /**
     * Create a new social post (draft or scheduled).
     */
    public function createPost(int $agencyId, array $data): SocialPost
    {
        return DB::transaction(function () use ($agencyId, $data) {
            $post = SocialPost::create([
                'agency_id' => $agencyId,
                'social_account_id' => $data['social_account_id'],
                'platform' => $data['platform'],
                'content' => $data['content'] ?? null,
                'media' => $data['media'] ?? null,
                'links' => $data['links'] ?? null,
                'hashtags' => $data['hashtags'] ?? null,
                'mentions' => $data['mentions'] ?? null,
                'tags' => $data['tags'] ?? null,
                'status' => $data['status'] ?? PostStatus::DRAFT->value,
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'published_at' => $data['published_at'] ?? null,
                'quality_score' => $data['quality_score'] ?? null,
            ]);

            return $post;
        });
    }

    /**
     * Schedule a post for future publishing.
     */
    public function schedulePost(int $agencyId, array $data): SocialPost
    {
        $data['status'] = PostStatus::SCHEDULED->value;

        return $this->createPost($agencyId, $data);
    }

    /**
     * Publish a post immediately.
     */
    public function publishPost(SocialPost $post): array
    {
        $post->update([
            'status' => PostStatus::PUBLISHING->value,
        ]);

        try {
            // Publish to platform
            $result = $this->publishToPlatform($post);

            $post->update([
                'status' => PostStatus::PUBLISHED->value,
                'published_at' => now(),
                'external_post_id' => $result['id'] ?? null,
                'platform_response' => $result,
            ]);

            return [
                'success' => true,
                'message' => 'Post published successfully.',
                'data' => $post,
            ];
        } catch (\Exception $e) {
            $post->update([
                'status' => PostStatus::FAILED->value,
                'failed_at' => now(),
                'error_message' => $e->getMessage(),
                'retry_count' => $post->retry_count + 1,
            ]);

            Log::error("Failed to publish post #{$post->id}: {$e->getMessage()}");

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => $post,
            ];
        }
    }

    /**
     * Publish to the social media platform.
     * (Stub — replace with actual API calls)
     */
    protected function publishToPlatform(SocialPost $post): array
    {
        $account = $post->socialAccount;
        if (! $account || ! $account->is_active || (int) $account->agency_id !== (int) $post->agency_id) {
            throw new \RuntimeException('A valid active social account is required.');
        }

        $result = $this->socialApi->publish($account, $post);
        if (! ($result['success'] ?? false) || empty($result['platform_post_id'])) {
            throw new \RuntimeException($result['error'] ?? 'The platform did not confirm publication.');
        }

        return ['id' => $result['platform_post_id'], ...$result];
    }

    protected function publishToFacebook(SocialPost $post): array
    {
        // Facebook Graph API integration
        return [
            'id' => 'fb_'.uniqid(),
            'platform' => 'facebook',
            'status' => 'published',
        ];
    }

    protected function publishToInstagram(SocialPost $post): array
    {
        // Instagram Graph API integration
        return [
            'id' => 'ig_'.uniqid(),
            'platform' => 'instagram',
            'status' => 'published',
        ];
    }

    protected function publishToTwitter(SocialPost $post): array
    {
        // Twitter/X API v2 integration
        return [
            'id' => 'tw_'.uniqid(),
            'platform' => 'twitter',
            'status' => 'published',
        ];
    }

    protected function publishToLinkedIn(SocialPost $post): array
    {
        // LinkedIn API integration
        return [
            'id' => 'li_'.uniqid(),
            'platform' => 'linkedin',
            'status' => 'published',
        ];
    }

    protected function publishToTikTok(SocialPost $post): array
    {
        // TikTok API integration
        return [
            'id' => 'tt_'.uniqid(),
            'platform' => 'tiktok',
            'status' => 'published',
        ];
    }

    protected function publishToPinterest(SocialPost $post): array
    {
        // Pinterest API integration
        return [
            'id' => 'pt_'.uniqid(),
            'platform' => 'pinterest',
            'status' => 'published',
        ];
    }

    /**
     * Get post statistics.
     */
    public function getPostStats(SocialPost $post): array
    {
        return [
            'views' => $post->views_count,
            'likes' => $post->likes_count,
            'comments' => $post->comments_count,
            'shares' => $post->shares_count,
            'clicks' => $post->clicks_count,
            'engagement_rate' => $post->engagement_rate,
        ];
    }

    /**
     * Retry a failed post.
     */
    public function retryPost(SocialPost $post): array
    {
        if ($post->retry_count >= 3) {
            return [
                'success' => false,
                'message' => 'Maximum retry attempts reached.',
            ];
        }

        return $this->publishPost($post);
    }
}
