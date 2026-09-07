<?php

namespace App\Services\Email;

use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SmtpEmailService
{
    public function sendCampaign(EmailCampaign $campaign): array
    {
        $results = [
            'sent' => 0,
            'failed' => 0,
            'total' => 0,
        ];

        $recipients = $campaign->recipients()->where('status', 'pending')->get();
        $results['total'] = $recipients->count();

        foreach ($recipients as $recipient) {
            try {
                $this->sendToRecipient($campaign, $recipient);
                $recipient->update(['status' => 'sent', 'sent_at' => now()]);
                $results['sent']++;
            } catch (\Exception $e) {
                $recipient->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                $results['failed']++;
                Log::error("Email send failed to {$recipient->email}: " . $e->getMessage());
            }
        }

        return $results;
    }

    public function sendToRecipient(EmailCampaign $campaign, EmailCampaignRecipient $recipient): void
    {
        Mail::html($campaign->content, function ($message) use ($campaign, $recipient) {
            $message->to($recipient->email, $recipient->name)
                ->subject($campaign->subject);
        });
    }

    public function sendTest(string $to, string $subject, string $content): bool
    {
        try {
            Mail::html($content, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            Log::error("Test email failed: " . $e->getMessage());
            return false;
        }
    }

    public function verifyConnection(): array
    {
        try {
            $transport = Mail::getSymfonyTransport();
            $transport->start();
            return ['success' => true, 'message' => 'SMTP connection verified'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
