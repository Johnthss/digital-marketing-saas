<?php

namespace Tests\Unit\Notifications;

use App\Models\Agency;
use App\Notifications\QuotaWarningNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function quota_warning_notification_can_be_created(): void
    {
        $agency = Agency::factory()->create();
        $notification = new QuotaWarningNotification($agency, 'posts', 90, 100);
        $this->assertNotNull($notification);
    }

    /** @test */
    public function notification_has_correct_via_channels(): void
    {
        $agency = Agency::factory()->create();
        $notification = new QuotaWarningNotification($agency, 'posts', 90, 100);
        $this->assertContains('mail', $notification->via(new \Illuminate\Notifications\AnonymousNotifiable()));
    }
}
