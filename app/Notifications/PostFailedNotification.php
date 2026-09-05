<?php

namespace App\Notifications;

use App\Models\SocialPost;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class PostFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public SocialPost $post,
        public string $errorMessage,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Post Failed to Publish')
            ->line("Your post #{$this->post->id} failed to publish to {$this->post->platform}.")
            ->line("Error: {$this->errorMessage}")
            ->action('Retry', url("/social/posts/{$this->post->id}"))
            ->line('Thank you for using ' . config('app.name') . '!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'platform' => $this->post->platform,
            'error' => $this->errorMessage,
            'message' => "Post #{$this->post->id} failed: {$this->errorMessage}",
            'url' => "/social/posts/{$this->post->id}",
        ];
    }
}
