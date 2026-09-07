<?php

namespace Tests\Unit\Models;

use App\Models\ActivityFeed;
use App\Models\Agency;
use App\Models\Comment;
use App\Models\ConsentRecord;
use App\Models\DataDeletionRequest;
use App\Models\DataExportRequest;
use App\Models\EmailTemplate;
use App\Models\MediaAsset;
use App\Models\Report;
use App\Models\WhiteLabelSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function media_asset_has_human_size_accessor(): void
    {
        $asset = MediaAsset::factory()->create(['file_size' => 1048576]);
        $this->assertEquals('1.00 MB', $asset->human_size);
    }

    /** @test */
    public function media_asset_has_thumbnail_url_accessor(): void
    {
        $asset = MediaAsset::factory()->create(['file_path' => 'media/1/test.jpg']);
        $this->assertStringContainsString('storage/media/1/test.jpg', $asset->thumbnail_url);
    }

    /** @test */
    public function white_label_has_display_name_accessor(): void
    {
        $setting = WhiteLabelSetting::factory()->create(['brand_name' => 'Test Brand']);
        $this->assertEquals('Test Brand', $setting->display_name);
    }

    /** @test */
    public function white_label_falls_back_to_app_name(): void
    {
        $setting = WhiteLabelSetting::factory()->create(['brand_name' => null]);
        $this->assertEquals(config('app.name'), $setting->display_name);
    }

    /** @test */
    public function email_template_renders_variables(): void
    {
        $template = EmailTemplate::factory()->create([
            'subject' => 'Hello {{ name }}',
            'html_content' => '<h1>Welcome {{ name }}</h1>',
        ]);
        $rendered = $template->render(['name' => 'John']);
        $this->assertEquals('Hello John', $rendered['subject']);
        $this->assertEquals('<h1>Welcome John</h1>', $rendered['html']);
    }

    /** @test */
    public function report_has_download_url_accessor(): void
    {
        $report = Report::factory()->create(['file_path' => 'reports/test.pdf']);
        $this->assertStringContainsString('storage/reports/test.pdf', $report->download_url);
    }

    /** @test */
    public function activity_feed_has_icon_accessor(): void
    {
        $activity = ActivityFeed::factory()->create(['action' => 'post_created']);
        $this->assertEquals('fa-pen-fancy', $activity->icon);
    }

    /** @test */
    public function activity_feed_has_description_accessor(): void
    {
        $activity = ActivityFeed::factory()->create(['action' => 'post_created']);
        $this->assertStringContainsString('created a new post', $activity->description);
    }

    /** @test */
    public function consent_record_has_granted_scope(): void
    {
        ConsentRecord::factory()->create(['consent_type' => 'marketing', 'granted' => true]);
        ConsentRecord::factory()->create(['consent_type' => 'analytics', 'granted' => false]);
        $this->assertEquals(1, ConsentRecord::granted()->count());
    }

    /** @test */
    public function data_deletion_request_has_overdue_scope(): void
    {
        DataDeletionRequest::factory()->create(['status' => 'pending', 'scheduled_at' => now()->subDay()]);
        DataDeletionRequest::factory()->create(['status' => 'pending', 'scheduled_at' => now()->addDays(30)]);
        $this->assertEquals(1, DataDeletionRequest::overdue()->count());
    }

    /** @test */
    public function comment_has_for_commentable_scope(): void
    {
        Comment::factory()->create(['commentable_type' => 'App\Models\SocialPost', 'commentable_id' => 1]);
        Comment::factory()->create(['commentable_type' => 'App\Models\SocialPost', 'commentable_id' => 2]);
        $this->assertEquals(1, Comment::forCommentable(new \App\Models\SocialPost(['id' => 1]))->count());
    }
}
