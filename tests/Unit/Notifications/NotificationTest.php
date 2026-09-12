<?php

namespace Tests\Unit\Notifications;

use App\Models\Agency;
use App\Notifications\QuotaWarningNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function quota_warning_notification_can_be_created(): void
    {
        $agency = Agency::factory()->create();
        $notification = new QuotaWarningNotification($agency, 'posts', 90, 100);
        $this->assertNotNull($notification);
    }

    #[Test]
    public function notification_has_correct_via_channels(): void
    {
        $agency = Agency::factory()->create();
        $notification = new QuotaWarningNotification($agency, 'posts', 90, 100);
        $this->assertContains('mail', $notification->via(new AnonymousNotifiable));
    }
}
