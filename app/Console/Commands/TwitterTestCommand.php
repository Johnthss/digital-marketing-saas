<?php

namespace App\Console\Commands;

use App\Services\Social\TwitterApiService;
use Illuminate\Console\Command;

class TwitterTestCommand extends Command
{
    protected $signature = 'twitter:test 
                            {--username= : Twitter username to fetch metrics for}
                            {--tweet-id= : Tweet ID to fetch metrics for}
                            {--auth : Test authentication only}';

    protected $description = 'Test Twitter/X API connection and fetch metrics';

    public function handle(TwitterApiService $twitter): int
    {
        $this->info('=== Twitter/X API Test ===');
        $this->newLine();

        // Check configuration
        if (! $twitter->isConfigured()) {
            $this->error('Twitter OAuth 1.0a credentials are not fully configured.');
            $this->warn('Please set TWITTER_API_KEY, TWITTER_API_SECRET, TWITTER_ACCESS_TOKEN, and TWITTER_ACCESS_SECRET in your .env file.');

            return self::FAILURE;
        }

        if (! $twitter->hasBearerToken()) {
            $this->warn('TWITTER_BEARER_TOKEN is not set. Some features may not work.');
        }

        // Test authentication
        $this->info('Testing authentication...');
        $auth = $twitter->authenticate();

        if ($auth['success']) {
            $data = $auth['data']['data'] ?? [];
            $this->info('✓ Authentication successful!');
            $this->line("  User ID: {$data['id']}");
            $this->line("  Username: {$data['username']}");
            $this->line("  Name: {$data['name']}");
            $metrics = $data['public_metrics'] ?? [];
            $this->line("  Followers: " . ($metrics['followers_count'] ?? 0));
            $this->line("  Following: " . ($metrics['following_count'] ?? 0));
            $this->line("  Tweets: " . ($metrics['tweet_count'] ?? 0));
        } else {
            $this->error('✗ Authentication failed: ' . ($auth['error'] ?? 'Unknown error'));
        }

        $this->newLine();

        // Get user metrics if username provided
        $username = $this->option('username');
        if ($username) {
            $this->info("Fetching metrics for @{$username}...");
            $metrics = $twitter->getUserMetrics($username);

            if ($metrics['success']) {
                $data = $metrics['data'];
                $this->info('✓ User metrics fetched successfully!');
                $this->line("  Followers: {$data['followers_count']}");
                $this->line("  Following: {$data['following_count']}");
                $this->line("  Tweets: {$data['tweet_count']}");
                $this->line("  Listed: {$data['listed_count']}");
            } else {
                $this->error('✗ Failed to fetch user metrics: ' . ($metrics['error'] ?? 'Unknown error'));
            }

            $this->newLine();
        }

        // Get tweet metrics if tweet-id provided
        $tweetId = $this->option('tweet-id');
        if ($tweetId) {
            $this->info("Fetching metrics for tweet {$tweetId}...");
            $metrics = $twitter->getTweetMetrics($tweetId);

            if ($metrics['success']) {
                $data = $metrics['data'];
                $this->info('✓ Tweet metrics fetched successfully!');
                $this->line("  Text: {$data['text']}");
                $this->line("  Retweets: {$data['retweet_count']}");
                $this->line("  Replies: {$data['reply_count']}");
                $this->line("  Likes: {$data['like_count']}");
                $this->line("  Quotes: {$data['quote_count']}");
                $this->line("  Impressions: {$data['impression_count']}");
            } else {
                $this->error('✗ Failed to fetch tweet metrics: ' . ($metrics['error'] ?? 'Unknown error'));
            }

            $this->newLine();
        }

        $this->info('Twitter/X API test complete.');

        return self::SUCCESS;
    }
}
