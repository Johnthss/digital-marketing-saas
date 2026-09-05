<?php

namespace App\Listeners\Social;

use App\Events\PostPublished;
use App\Events\PostFailed;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class SendPostNotification
{
    public function handlePostPublished(PostPublished $event): void
    {
        $agency = $event->post->agency;
        $recipients = User::where('agency_id', $agency->id)->get();
        // TODO: Send notification when notification class is ready
    }

    public function handlePostFailed(PostFailed $event): void
    {
        $agency = $event->post->agency;
        $recipients = User::where('agency_id', $agency->id)
            ->whereIn('role', ['owner', 'admin'])
            ->get();
        // TODO: Send notification
    }
}
